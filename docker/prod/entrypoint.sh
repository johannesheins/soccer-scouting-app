#!/bin/sh
set -e

rm -f public/hot

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php-fpm -F