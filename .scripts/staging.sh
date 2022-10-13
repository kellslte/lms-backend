#!/bin/bash
set -e

echo "Deployment started..."


# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true

# Pull the latest version of the app
git fetch origin staging
git reset --hard origin/staging

# Install dependencies based on lock file
composer install --no-interaction --prefer-dist --optimize-autoloader

# recreate database
php artisan migrate:fresh --seed

# Recreate cache
php artisan optimize

# cache events
php artisan event:cache

# Exit maintenance mode
php artisan up

# Remove all the uploaded files on the server
cd public/uploads/lessons && rm -rf ./*
cd ../thumbnails && rm -rf ./*
cd ../transcripts && rm -rf ./*
cd ../../../

echo "Deployment finished!"
