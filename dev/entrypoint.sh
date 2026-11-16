#!/bin/sh
# First-run bootstrap for the WhereTo dev container, then exec the given command.
set -eu

cd /app

if [ ! -f .env ]; then
  echo "[entrypoint] creating .env from dev/laravel.env"
  cp /dev-config/laravel.env .env
fi

if [ ! -f vendor/autoload.php ]; then
  echo "[entrypoint] composer install (first run, takes a minute)"
  composer install --no-interaction --prefer-dist
fi

if ! grep -q '^APP_KEY=base64:' .env; then
  echo "[entrypoint] generating APP_KEY"
  php artisan key:generate --force
fi

# Wait for PostgreSQL even though compose already gates on the healthcheck.
i=0
until php -r 'new PDO("pgsql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' 2>/dev/null; do
  i=$((i+1)); [ "$i" -gt 30 ] && { echo "[entrypoint] database not reachable"; exit 1; }
  sleep 1
done

echo "[entrypoint] running migrations"
php artisan migrate --force

if [ ! -f storage/.dev-seeded ]; then
  echo "[entrypoint] seeding the database (first run)"
  php artisan db:seed --force
  touch storage/.dev-seeded
fi

exec "$@"
