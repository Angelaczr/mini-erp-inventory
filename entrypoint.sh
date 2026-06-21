#!/bin/bash
set -e

# Generate app key if not already set via Render env vars
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config for performance
php artisan config:cache
php artisan route:cache

# Run any pending migrations (safe to run on every boot — does nothing if already up to date)
php artisan migrate --force

# Seed demo data only if the database is empty (DatabaseSeeder guards against
# duplicate inserts, so this is safe to run on every boot too)
php artisan db:seed --force

# Start Apache in the foreground
apache2-foreground
