#!/usr/bin/env bash
set -euo pipefail

# This script provisions Ubuntu 24.04 for the ShortV1 app with Laravel, PHP 8.4, SQLite, and Nginx/Forge friendly
# It creates a fresh Laravel app into /var/www/shortv1 and applies this repository's overlay.

APP_DIR=/var/www/shortv1
APP_NAME=shortv1
PHP_VERSION=8.4

sudo apt-get update
sudo apt-get install -y software-properties-common curl unzip git sqlite3

# Install PHP 8.4 + extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php$PHP_VERSION php$PHP_VERSION-cli php$PHP_VERSION-fpm php$PHP_VERSION-mbstring php$PHP_VERSION-xml php$PHP_VERSION-curl php$PHP_VERSION-sqlite3 php$PHP_VERSION-zip

# Install Composer
EXPECTED_SIGNATURE="$(curl -s https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then >&2 echo 'ERROR: Invalid composer installer signature'; rm composer-setup.php; exit 1; fi
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

sudo mkdir -p $APP_DIR
sudo chown -R $USER:www-data $APP_DIR
cd $APP_DIR

# Create Laravel project
composer create-project laravel/laravel . --no-interaction

# Require packages
composer require guzzlehttp/guzzle jenssegers/agent

# Configure environment
cp .env.example .env
php artisan key:generate

# SQLite setup
mkdir -p database
: > database/database.sqlite
php -r "file_put_contents('.env', preg_replace('/^DB_CONNECTION=.*/m','DB_CONNECTION=sqlite', file_get_contents('.env')));"
php -r "file_put_contents('.env', preg_replace('/^DB_DATABASE=.*/m','DB_DATABASE='$(pwd)'/database/database.sqlite', file_get_contents('.env')));"

# Apply overlay files from this repo's overlay directory if present
if [ -d "$OLDPWD/overlay" ]; then
  rsync -a "$OLDPWD/overlay/" "$APP_DIR/"
fi

# Register StopBot middleware in kernel
php -r "
$k = file_get_contents('app/Http/Kernel.php');
$k = str_replace('protected $middlewareAliases = [', 'protected $middlewareAliases = [\n        \'stopbot\' => \\App\\Http\\Middleware\\StopBotMiddleware::class,', $k);
file_put_contents('app/Http/Kernel.php', $k);
"

# Migrate
php artisan migrate --force

# Remove tests as requested
rm -rf tests

# Permissions
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

# Done
echo "ShortV1 installed in $APP_DIR"
