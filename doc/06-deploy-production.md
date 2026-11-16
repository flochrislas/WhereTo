# Production deployment

Target: one Linux server (Ubuntu 24.04 LTS or Debian 12), Nginx, PHP-FPM, PostgreSQL, HTTPS through Let's Encrypt. The app is small and read-mostly, so a single small VM is plenty. The same steps work for the legacy stack (PHP 7.4, Laravel 5.8) and for the upgraded one (PHP 8.5, Laravel 13); differences are called out.

HTTPS is not optional: the browser geolocation API only works on secure origins, and the whole "near first" sorting depends on it.

## 1. System packages

```sh
sudo apt update
sudo apt install nginx postgresql composer git unzip certbot python3-certbot-nginx
# PHP: pick ONE line
sudo apt install php8.5-fpm php8.5-pgsql php8.5-mbstring php8.5-xml php8.5-bcmath php8.5-curl php8.5-zip php8.5-intl   # upgraded app
# or, for the legacy app, add the ondrej/php PPA (Ubuntu) and install the php7.4-* equivalents
```

On Ubuntu 24.04 the `php8.5` packages come from the `ppa:ondrej/php` repository; Debian users can use `packages.sury.org`.

## 2. Database

```sh
sudo -u postgres psql <<'SQL'
CREATE USER wheretouser WITH PASSWORD 'a-long-random-password';
CREATE DATABASE whereto OWNER wheretouser;
SQL
```

The application only needs `CONNECT`, `CREATE` (for migrations) and DML rights on its own database.

## 3. Deploy the code

Run as a deploy user that owns the files; PHP-FPM runs as `www-data`.

```sh
sudo mkdir -p /var/www/whereto && sudo chown "$USER" /var/www/whereto
git clone https://github.com/flochrislas/WhereTo.git /var/www/whereto
cd /var/www/whereto/source
composer install --no-dev --optimize-autoloader --no-interaction
```

`--no-dev` leaves out Debugbar, ide-helper, PHPUnit and Faker. The app still boots because `AppServiceProvider` only registers ide-helper outside production, but `config/app.php` lists `Barryvdh\Debugbar\ServiceProvider` unconditionally and the models call `\Debugbar::debug()`. With `--no-dev` those classes are missing and the app crashes. Choose one:

- keep `composer install` without `--no-dev` and set `DEBUGBAR_ENABLED=false` (simplest for the legacy app), or
- remove the Debugbar provider and alias from `config/app.php` and the `\Debugbar::` calls from the four place models (done anyway during the upgrade).

## 4. Environment file

Create `/var/www/whereto/source/.env` (never commit it):

```ini
APP_NAME=WhereTo
APP_ENV=production
APP_KEY=                 # filled by key:generate below
APP_DEBUG=false
APP_URL=https://your.domain
APP_LOG=daily
APP_LOG_LEVEL=info       # the code logs at debug level everywhere; keep this at info or warning
POORLY_COPIED=nope

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=whereto
DB_USERNAME=wheretouser
DB_PASSWORD=a-long-random-password

CACHE_DRIVER=database    # or file; redis if you install it
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
QUEUE_DRIVER=sync
MAIL_DRIVER=log          # set a real mailer only if you need password resets
DEBUGBAR_ENABLED=false
```

For Laravel 13 the variable names are `LOG_CHANNEL`, `LOG_LEVEL`, `CACHE_STORE`, `QUEUE_CONNECTION`, `MAIL_MAILER` (see 07-upgrade-2026.md).

Then:

```sh
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force        # first deploy only, or whenever seed data changed
php artisan config:cache
php artisan route:cache            # fails on the legacy app because routes use closures; skip it there
php artisan view:cache
```

`route:cache` requires every route to point at a controller; the committed `routes/web.php` uses closures for the home, info, vip, about, test and admin pages, so on the legacy app only `config:cache` and `view:cache` apply.

## 5. Permissions

```sh
sudo chown -R "$USER":www-data /var/www/whereto/source
sudo chmod -R g+w /var/www/whereto/source/storage /var/www/whereto/source/bootstrap/cache
```

Only `storage/` and `bootstrap/cache/` need to be writable by PHP-FPM.

## 6. Nginx

`/etc/nginx/sites-available/whereto`:

```nginx
server {
    listen 80;
    server_name your.domain;
    root /var/www/whereto/source/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;   # php7.4-fpm.sock for the legacy app
    }

    location ~ /\.(?!well-known).* { deny all; }

    location ~* \.(css|js|woff2?|ttf|svg|ico)$ {
        expires 30d;
        access_log off;
    }
}
```

```sh
sudo ln -s /etc/nginx/sites-available/whereto /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d your.domain        # obtains the certificate and adds the HTTPS server block + redirect
```

Certbot installs a systemd timer for renewal. If you terminate TLS on a proxy or load balancer in front of Nginx instead, set `TrustProxies::$proxies` (or `trustProxies()` in `bootstrap/app.php` on Laravel 13) so that `url()` and `SESSION_SECURE_COOKIE` see the original scheme.

Apache users can keep the committed `public/.htaccess` with `mod_rewrite` and `AllowOverride All`.

## 7. Hardening checklist

- `APP_DEBUG=false`. The admin layout prints config values when debug is on.
- Registration is open at `/register`. Either remove `register` from `Auth::routes(['register' => false])` (Laravel 5.8 supports this option) or delete the route after creating your admin account.
- Change the seeded users' passwords (`password`) or delete `user2@test.rt`:

  ```sh
  php artisan tinker --execute="App\User::where('email','user2@test.rt')->delete();"
  ```

- The `/clubs*`, `/about`, `/admin/restaurants/import` and `export` routes point to missing code and return 500s. Harmless, but remove them if you care about clean logs.
- Google Analytics uses a Universal Analytics id that no longer collects anything; remove `inline-javascript/google-analytics.blade.php` or replace it with a GA4 tag.
- Keep `storage/logs` rotated: the `daily` channel already rotates by day; add `logrotate` or cron cleanup for old files.

## 8. Updating a running deployment

```sh
cd /var/www/whereto && git pull
cd source
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force      # only when seeders changed; it wipes and reloads every table
php artisan cache:clear
php artisan config:cache && php artisan view:cache && php artisan route:cache
sudo systemctl reload php8.5-fpm
```

Add `php artisan down` / `php artisan up` around it if the seeding takes more than a second or two.

## 9. Backups

The source of truth for the data is the seeders in git, so a database dump is only needed if you ever add content through the admin:

```sh
pg_dump -U wheretouser -Fc whereto > whereto-$(date +%F).dump
```

Also keep `.env` somewhere safe; it holds the `APP_KEY` that encrypts sessions and cookies.

## 10. Alternative hosting

- **Laravel Forge / Ploi**: point them at the repository with `source` as the project root; they generate the Nginx config, PHP-FPM pool, certbot and the deploy script above.
- **Containers**: a `php:8.5-fpm-alpine` image plus an Nginx sidecar works; mount nothing writable except `storage`. Switch `CACHE_DRIVER` and `SESSION_DRIVER` to `database` or `redis` so that multiple replicas share state.
- **Shared hosting with Apache**: upload `source/`, set the vhost document root to `source/public`, and run the artisan commands through SSH. The committed `.htaccess` handles the rewrite.
