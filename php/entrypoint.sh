#!/bin/sh
set -e
cd /var/www/html

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader
fi

if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    cp .env.example .env
fi
php artisan key:generate --force || true

php artisan optimize:clear || true
php artisan config:clear   || true
php artisan route:clear    || true
php artisan view:clear     || true
php artisan event:clear    || true

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    if [ "${MIGRATE_FRESH:-0}" = "1" ]; then
        php artisan migrate:fresh --force || true
    else
        php artisan migrate --force || true
    fi
    if [ "${RUN_SEED:-0}" = "1" ]; then
        php artisan db:seed --force || true
    fi
fi
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true
exec "$@"
