#!/bin/bash
set -e

echo "Production deployment started..."


# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true

# Pull the latest version of the app
git fetch origin prod
git reset --hard origin/prod

# Install dependencies based on lock file
composer install --no-interaction --prefer-dist --optimize-autoloader

# Update Database
php artisan migrate --force

# Recreate cache
php artisan optimize

# cache events
php artisan event:cache 


# Exit maintenance mode
php artisan up

echo "Deployment finished!"