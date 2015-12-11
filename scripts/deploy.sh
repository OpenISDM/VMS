#!/bin/sh

echo 'Run heroku-deploy.sh'
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
php artisan db:seed --class=CountryCityTableSeeder
