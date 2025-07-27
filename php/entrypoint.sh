#!/bin/sh

# Instalar dependências do PHP
if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction --optimize-autoloader
fi

if [ ! -d "vendor/laravel/sanctum" ]; then
    composer require laravel/sanctum --no-interaction
    php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --tag="config"
    php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --tag="migrations"
fi

if [ ! -d "vendor/prettus/l5-repository" ]; then
    composer require prettus/l5-repository --no-interaction
    php artisan vendor:publish --provider="Prettus\Repository\Providers\RepositoryServiceProvider"
fi

# Configurar ambiente
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Instalar Breeze com Inertia Vue
if [ ! -d "vendor/laravel/breeze" ]; then
    composer require laravel/breeze --dev -n
    php artisan breeze:install --dark <<EOF
1
0
0
EOF
fi

# Corrigir permissões
chmod -R 755 vendor
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache


# Executar migrações
php artisan migrate --force
php artisan db:seed --force
exec "$@"