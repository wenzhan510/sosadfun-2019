#!/bin/bash
chmod 777 ./wait-for-it.sh
./wait-for-it.sh -h $DB_HOST -p $DB_PORT -t 120
if [ ! -f "./created.tag" ]; then
    php artisan migrate --seed
    php artisan key:generate
    php artisan passport:keys
    php artisan passport:install
    touch "created.tag"
fi
php artisan serve --host=0.0.0.0  --port=8000