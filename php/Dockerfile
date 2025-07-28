FROM php:8.2-fpm

# Ambiente
WORKDIR /var/www/html
RUN docker-php-ext-install pdo_mysql
# Instalar dependências básicas
RUN apt-get update && apt-get install -y \
    git curl zip unzip gnupg2 nano apt-transport-https \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*


# Configurações do OPCache e PHP
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=32" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=60" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "memory_limit=6G" > /usr/local/etc/php/conf.d/memory-limit.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Copiar entrypoint
COPY php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh


ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
