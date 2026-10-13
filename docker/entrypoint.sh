#!/bin/bash

# Port binding: Render dynamically assigns $PORT (default 10000 on Render)
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT}..."
sed -i "s/Listen .*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure storage and bootstrap/cache directories exist with proper permissions
echo "==> Setting directory permissions..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# SQLite support if sqlite is used
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

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    echo "==> [NOTICE] APP_KEY is missing from environment. Generating fallback key..."
    php artisan key:generate --force || true
fi

# Refresh configuration caches
echo "==> Clearing cached configurations..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations with error trapping so container never crashes
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    if php artisan migrate --force --no-interaction; then
        echo "==> Database migrations completed successfully."
    else
        echo "==> [WARNING] Database migrations failed. Please verify database credentials in Render."
    fi
fi

# Run seeders if RUN_SEEDERS=true
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Seeding initial data..."
    if php artisan db:seed --force --no-interaction; then
        echo "==> Database seeded successfully."
    else
        echo "==> [WARNING] Database seeding failed or already seeded."
    fi
fi

# Production optimization
if [ "${APP_ENV}" = "production" ]; then
    echo "==> Caching configurations for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting Apache web server on port ${PORT}..."
exec apache2-foreground
