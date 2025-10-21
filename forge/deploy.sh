#!/usr/bin/env bash
set -euo pipefail

# Forge deploy script for ShortV1
# Assumes PHP 8.4 and site root points to this repository

php -v
composer --version || true

# Ensure SQLite file exists
mkdir -p database
if [ ! -f database/database.sqlite ]; then
  : > database/database.sqlite
fi

# Ensure env vars
if [ ! -f .env ]; then
  cp .env.example .env || true
fi

# Force SQLite settings in .env if DB_CONNECTION not set
if ! grep -q '^DB_CONNECTION=sqlite' .env; then
  sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env || true
fi
if ! grep -q '^DB_DATABASE=' .env; then
  echo "DB_DATABASE=$(pwd)/database/database.sqlite" >> .env
fi

php artisan down || true

composer install --no-interaction --prefer-dist --no-dev -o
php artisan key:generate --force || true
php artisan migrate --force

# Remove tests per requirement
rm -rf tests || true

php artisan optimize
php artisan up

# Permissions for storage
chgrp -R www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

echo "Forge deploy completed."