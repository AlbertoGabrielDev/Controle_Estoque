#!/bin/sh
set -e
cd /var/www/html

echo "[entrypoint] preparando storage e caches..."
mkdir -p \
  bootstrap/cache \
  storage/framework/cache \
  storage/framework/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache


if [ ! -f ".env" ] && [ -f ".env.example" ]; then
  cp .env.example .env
fi

if [ -f ".env" ] && grep -q "^VIEW_COMPILED_PATH=" .env; then
  sed -i "/^VIEW_COMPILED_PATH=/d" .env
  echo "[entrypoint] removido VIEW_COMPILED_PATH do .env (usar storage/framework/views)"
fi

if [ ! -f "vendor/autoload.php" ]; then
  echo "[entrypoint] instalando dependências do composer..."
  composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader
fi

# --- App key ---
php artisan key:generate --force || true

# --- Limpar caches velhos no filesystem (antes do artisan) ---
# (alguns nomes variam por versão; apagar tudo em bootstrap/cache é seguro)
find bootstrap/cache -type f -name "*.php" -delete || true

# --- Limpar caches do Laravel (uma vez no start) ---
php artisan optimize:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan event:clear || true

# --- Permissões finais ---
chown -R www-data:www-data storage bootstrap/cache vendor || true
chmod -R 775 storage bootstrap/cache || true

echo "[entrypoint] pronto. iniciando processo..."
exec "$@"
