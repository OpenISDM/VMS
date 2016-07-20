#!/bin/bash

echo 'Run deploy.sh'
php artisan migrate
php artisan db:seed
php artisan db:seed --class=CountryCityTableSeeder
