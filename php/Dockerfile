FROM php:8.2-fpm

WORKDIR /var/www/html

# Dependências do PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip gnupg2 nano apt-transport-https \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP ini (DEV): desliga OPcache no FPM e mostra erros
RUN { \
  echo "display_errors=On"; \
  echo "error_reporting=E_ALL"; \
  echo "log_errors=On"; \
  echo "memory_limit=6G"; \
  # OPcache DEV: desligado para resposta imediata a mudanças
  echo "opcache.enable=0"; \
  echo "opcache.enable_cli=0"; \
  echo "opcache.validate_timestamps=1"; \
  echo "opcache.revalidate_freq=0"; \
  echo "opcache.max_accelerated_files=100000"; \
  echo "opcache.memory_consumption=256"; \
  echo "opcache.interned_strings_buffer=32"; \
} > /usr/local/etc/php/conf.d/zz-dev.ini

# Entrypoint
COPY php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
