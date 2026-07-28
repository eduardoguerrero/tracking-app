# Stage 1: Build dependencies
FROM php:8.3-cli-alpine AS builder

RUN apk add --no-cache unzip curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader

# Stage 2: Runtime
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    icu-dev \
    && docker-php-ext-install pdo pdo_mysql intl \
    && docker-php-ext-enable pdo_mysql

COPY --from=builder /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY --from=builder /app/vendor ./vendor
COPY . .

RUN cp .env.docker .env \
    && composer dump-autoload --optimize \
    && php artisan package:discover

RUN mkdir -p /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
