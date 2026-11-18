# Production deployment

Target: one Linux server (Ubuntu 24.04 LTS or Debian 12), Nginx, PHP-FPM, PostgreSQL, HTTPS through Let's Encrypt. The app is small and read-mostly, so a single small VM is plenty. These steps are for the upgraded app (PHP 8.5, Laravel 13).

HTTPS is not optional: the browser geolocation API and the service worker (installable app, offline) only work on secure origins, and the whole "near first" sorting depends on it.

## 1. System packages

```sh
sudo apt update
sudo apt install nginx postgresql composer git unzip certbot python3-certbot-nginx
sudo apt install php8.5-fpm php8.5-pgsql php8.5-mbstring php8.5-xml php8.5-bcmath php8.5-curl php8.5-zip php8.5-intl
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

`--no-dev` leaves out Debugbar, ide-helper, Larastan, Pint and PHPUnit. The app does not reference any of them at runtime (verified with `php artisan about` on a `--no-dev` install).

## 4. Environment file

Create `/var/www/whereto/source/.env` (never commit it):

```ini
APP_NAME=WhereTo
APP_ENV=production
APP_KEY=                 # filled by key:generate below
APP_DEBUG=false
APP_URL=https://your.domain   # also what sitemap.xml and robots.txt print

LOG_CHANNEL=daily
LOG_LEVEL=info           # the code logs at debug level everywhere; keep this at info or warning

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=whereto
DB_USERNAME=wheretouser
DB_PASSWORD=a-long-random-password

CACHE_STORE=database     # or file; redis if you install it
SESSION_DRIVER=database  # the sessions table exists; file works too
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
MAIL_MAILER=log
DEBUGBAR_ENABLED=false
```

Then:

```sh
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force        # first deploy only, or whenever seed data changed
php artisan optimize                # config, routes, views and events cached in one go
```

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

    # Compress text responses (HTML pages and fragments, CSS, JS, manifest, SVG)
    gzip on;
    gzip_types text/plain text/css application/javascript application/json image/svg+xml;
    gzip_min_length 1024;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
    }

    location ~ /\.(?!well-known).* { deny all; }

    # The service worker must never be cached for long, or browsers keep an old version for days
    location = /sw.js {
        add_header Cache-Control "no-cache";
    }

    # Static files. The stylesheet and the script are linked with a ?v= that changes with the file,
    # so the browser may keep them for a year without ever asking again ("immutable").
    location ~* \.(css|js|woff2|svg|ico|png|json)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
```

```sh
sudo ln -s /etc/nginx/sites-available/whereto /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d your.domain        # obtains the certificate and adds the HTTPS server block + redirect
```

In the HTTPS server block that certbot adds, enable HTTP/2 (one connection for all the files, fewer round trips on mobile): `listen 443 ssl; http2 on;` (Nginx 1.25+; older versions use `listen 443 ssl http2;`).

Certbot installs a systemd timer for renewal. If you terminate TLS on a proxy or load balancer in front of Nginx instead, add `$middleware->trustProxies(at: '<proxy ip>')` in `bootstrap/app.php` so that `url()` and `SESSION_SECURE_COOKIE` see the original scheme.

Apache users can keep the committed `public/.htaccess` with `mod_rewrite` and `AllowOverride All`.

## 7. OPcache

PHP-FPM compiles every PHP file on each request unless OPcache keeps the compiled code in memory. It is on by default in Ubuntu's packages, but the production settings are worth setting in `/etc/php/8.5/fpm/conf.d/99-whereto.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=20000
; Never check the files for changes: the code only changes at deploy time, and the deploy restarts PHP-FPM
opcache.validate_timestamps=0
```

With `validate_timestamps=0`, PHP-FPM must be **restarted** (not just reloaded) after every deploy, otherwise it keeps running the old code; the update steps in section 9 do that. Leave `validate_timestamps` at its default of 1 if that is a problem.

Together with `php artisan optimize` (configuration, routes, views and events compiled once) this is where the server time per request goes from tens of milliseconds to a few.

## 8. Hardening checklist

- `APP_DEBUG=false`.
- There is no login and no admin: the site is read-only and has no accounts. The data is changed by editing the seeders and redeploying (section 9).
- `GET /up` is the framework health endpoint; restrict it at the proxy if you do not want it public.
- Keep `storage/logs` rotated: the `daily` channel already rotates by day (14 days kept by default).

## 9. Updating a running deployment

```sh
cd /var/www/whereto && git pull
cd source
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --force      # only when seeders changed; it wipes and reloads every table
php artisan cache:clear
php artisan optimize
sudo systemctl restart php8.5-fpm   # restart, not reload: drops the OPcache of the old code (section 7)
```

The stylesheet and the script are linked with their modification time, and the service worker refreshes its copies in the background, so visitors get the new front-end files on their next load without any manual cache busting.

Add `php artisan down` / `php artisan up` around it if the seeding takes more than a second or two.

A database created before November 2026 (when the admin and its `users` tables were removed) upgrades with the same commands: the sessions migration skips the table it already has, and a one-off migration drops `users`, `password_reset_tokens` and `user_restaurant`. Nothing is lost: the seeders hold the data.

## 10. Backups

The source of truth for the data is the seeders in git, so a database dump is not needed; the database can be rebuilt at any time with `php artisan migrate:fresh --seed --force`. If you still want one:

```sh
pg_dump -U wheretouser -Fc whereto > whereto-$(date +%F).dump
```

Also keep `.env` somewhere safe; it holds the `APP_KEY` that encrypts sessions and cookies.

## 11. Alternative hosting

- **Laravel Forge / Ploi**: point them at the repository with `source` as the project root; they generate the Nginx config, PHP-FPM pool, certbot and the deploy script above.
- **Containers**: a `php:8.5-fpm-alpine` image plus an Nginx sidecar works (the `dev/Containerfile` shows the extensions needed); mount nothing writable except `storage`. Use `CACHE_STORE` and `SESSION_DRIVER` `database` or `redis` so that multiple replicas share state.
- **Shared hosting with Apache**: upload `source/`, set the vhost document root to `source/public`, and run the artisan commands through SSH. The committed `.htaccess` handles the rewrite.
