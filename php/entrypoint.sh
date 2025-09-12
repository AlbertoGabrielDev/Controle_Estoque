#!/bin/sh
set -e
cd /var/www/html

# --- Garantir diretórios e permissões ANTES do composer/artisan ---
mkdir -p bootstrap/cache \
         storage/framework/{cache,data,sessions,testing,views} \
         storage/logs
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# --- Dependências ---
if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader
fi

# --- App key / .env ---
if [ ! -f ".env" ] && [ -f ".env.example" ]; then
    cp .env.example .env
fi
php artisan key:generate --force || true

# --- Limpar caches só no start (não a cada request) ---
php artisan optimize:clear || true

# --- Permissões finais (volumes podem “zerar” na primeira subida) ---
chown -R www-data:www-data storage bootstrap/cache vendor || true
chmod -R 775 storage bootstrap/cache || true

exec "$@"
