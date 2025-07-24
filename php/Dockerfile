FROM php:8.2-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    npm nodejs

# Instalar extensões necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip mbstring gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js via NVM para garantir versão moderna (ex: 18.x)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && npm install -g npm

WORKDIR /var/www
