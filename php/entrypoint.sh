#!/bin/bash

set -e

echo "✅ Iniciando container Laravel..."

# Corrigir permissões do projeto
chown -R www-data:www-data /var/www

# Instalar dependências PHP se vendor/ não existe
if [ ! -d "vendor" ]; then
  echo "📦 Instalando dependências PHP..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Instalar dependências Node se node_modules/ não existe
if [ ! -d "node_modules" ]; then
  echo "📦 Instalando dependências JavaScript..."
  npm install
fi

# Subir o PHP-FPM
echo "🚀 Iniciando PHP-FPM..."
exec php-fpm
