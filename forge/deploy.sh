#!/usr/bin/env bash
set -euo pipefail

# Forge deploy script for ShortV1
# This script supports two cases:
# 1) Repo already contains a full Laravel app (composer.json present)
# 2) Repo only contains the overlay/ folder — this script will bootstrap Laravel

echo "PHP version:" && php -v || true
composer --version || true

if [ ! -f composer.json ]; then
  echo "No composer.json found. Bootstrapping a new Laravel app..."
  # Create Laravel skeleton
  composer create-project laravel/laravel . --no-interaction
  # Require needed packages
  composer require guzzlehttp/guzzle jenssegers/agent --no-interaction
  # Apply overlay if exists in release
  if [ -d overlay ]; then
    if command -v rsync >/dev/null 2>&1; then
      rsync -a overlay/ ./
    else
      cp -R overlay/. ./
    fi
  fi
fi

# Ensure .env exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Ensure SQLite setup
mkdir -p database
if [ ! -f database/database.sqlite ]; then
  : > database/database.sqlite
fi

# Ensure env values for SQLite
if [ -f .env ]; then
  if ! grep -q '^DB_CONNECTION=sqlite' .env; then
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env || echo "DB_CONNECTION=sqlite" >> .env
  fi
  if ! grep -q '^DB_DATABASE=' .env; then
    echo "DB_DATABASE=$(pwd)/database/database.sqlite" >> .env
  fi
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