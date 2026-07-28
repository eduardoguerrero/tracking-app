#!/bin/sh
set -e

echo "Waiting for MySQL..."
until php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); echo 'OK'; } catch (Exception \$e) { exit(1); }" > /dev/null 2>&1; do
    sleep 2
done
echo "MySQL is ready."

php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force 2>/dev/null || echo "Seed data already exists, skipping."

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
