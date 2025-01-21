#!/bin/bash

if [ ! -f ".env" ]; then
    echo "Creating env file for env $APP_ENV"
    cp .env.example .env
else
    echo "env file exists."
fi

composer install --no-progress --no-interaction

php artisan key:generate
php artisan migrate
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan queue:work --timeout=60 &

php-fpm -D
nginx -g "daemon off;"
