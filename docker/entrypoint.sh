#!/usr/bin/env bash
set -e

# Ensure writable dirs
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/build 2>/dev/null || true
chmod -R 775 storage bootstrap/cache public/build 2>/dev/null || true

# Generate APP_KEY if missing (Render doesn't persist .env)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --ansi --force
fi

# Cache config / routes / views for production
php artisan config:cache --ansi 2>/dev/null || true
php artisan route:cache --ansi 2>/dev/null || true
php artisan view:cache --ansi 2>/dev/null || true

# Run migrations (Database must be provisioned before first deploy)
php artisan migrate --force --ansi 2>/dev/null || true

# Start Apache
exec apache2-foreground
