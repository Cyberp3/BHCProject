#!/bin/bash
set -e

PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

echo "==> Preparing storage and cache directories..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# SQLite support if selected
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    DB_DIR=$(dirname "$DB_PATH")
    mkdir -p "$DB_DIR"
    if [ ! -f "$DB_PATH" ]; then
        echo "==> Creating SQLite database at ${DB_PATH}..."
        touch "$DB_PATH"
    fi
    chown -R www-data:www-data "$DB_DIR" "$DB_PATH"
    chmod 775 "$DB_DIR"
    chmod 664 "$DB_PATH"
fi

# Clear stale caches
echo "==> Refreshing configuration caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force --isolated || php artisan migrate --force
fi

# Run seeders if RUN_SEEDERS=true
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Seeding initial database data..."
    php artisan db:seed --force
fi

# Optimize for production
if [ "${APP_ENV}" = "production" ]; then
    echo "==> Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "==> Starting Apache on port ${PORT}..."
exec apache2-foreground

