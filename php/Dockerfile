FROM php:8.2-fpm

WORKDIR /var/www/html

# Dependências do sistema + extensões PHP (pdo_mysql, gd, zip, redis)
RUN apt-get update && apt-get install -y \
    git curl zip unzip gnupg2 nano apt-transport-https \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP ini (dev rápido): OPcache ligado + realpath cache
RUN { \
  echo "display_errors=On"; \
  echo "error_reporting=E_ALL"; \
  echo "log_errors=On"; \
  echo "memory_limit=2G"; \
  echo "opcache.enable=1"; \
  echo "opcache.enable_cli=0"; \
  echo "opcache.validate_timestamps=1"; \
  echo "opcache.revalidate_freq=0"; \
  echo "opcache.max_accelerated_files=100000"; \
  echo "opcache.memory_consumption=256"; \
  echo "opcache.interned_strings_buffer=32"; \
  echo "realpath_cache_size=4096K"; \
  echo "realpath_cache_ttl=600"; \
} > /usr/local/etc/php/conf.d/zz-dev.ini

# PHP-FPM tuning (aproveita seu 5800X)
RUN { \
  echo "[www]"; \
  echo "user = www-data"; \
  echo "group = www-data"; \
  echo "listen = 9000"; \
  echo "pm = dynamic"; \
  echo "pm.max_children = 16"; \
  echo "pm.start_servers = 4"; \
  echo "pm.min_spare_servers = 2"; \
  echo "pm.max_spare_servers = 6"; \
  echo "pm.max_requests = 500"; \
  echo "request_terminate_timeout = 120s"; \
} > /usr/local/etc/php-fpm.d/zz-www.conf

# Entrypoint: copia, normaliza CRLF->LF e garante permissão de execução
COPY php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod 755 /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
