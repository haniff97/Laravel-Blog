#!/bin/bash
# Generate app key if not set
php artisan key:generate --force 2>/dev/null || true

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground (keeps container alive)
nginx -g "daemon off;"
