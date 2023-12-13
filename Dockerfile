# Use the official PHP image with ZTS on Alpine
FROM php:zts-alpine3.18

# Install necessary extensions and tools
RUN docker-php-ext-install pdo pdo_mysql
RUN apk --update --no-cache add nginx

# Copy Nginx configuration file
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Expose ports
EXPOSE 80

# Start Nginx and PHP
CMD ["sh", "-c", "nginx && php -S 0.0.0.0:80"]