# Utiliza a imagem oficial do PHP com suporte ao FPM (FastCGI Process Manager)
FROM php:8.2-apache

# Instala as dependências necessárias
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip

# Instala o Composer (gerenciador de dependências do PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN a2enmod rewrite



# Define o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80

CMD["apache2-foreground"]