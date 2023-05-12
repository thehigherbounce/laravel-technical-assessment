#!/bin/bash

# Wait for MySQL to start
until nc -z -v -w30 db 3306
do
  echo "Waiting for database connection..."
  sleep 5
done
echo "Database connection established!"

composer update
composer install --no-interaction
# Run database migrations
php artisan migrate:refresh --seed
php artisan db:seed

# Start Apache web server
apache2-foreground