# PowerShell setup for local dev on Windows
param(
  [string]$AppDir = "$PSScriptRoot/..\..\shortv1"
)

$ErrorActionPreference = 'Stop'

# Ensure Composer exists or install to user profile
function Ensure-Composer {
  if (Get-Command composer -ErrorAction SilentlyContinue) { return }
  Write-Host 'Installing Composer...'
  $installer = "$env:TEMP\composer-setup.php"
  Invoke-WebRequest https://getcomposer.org/installer -OutFile $installer
  php $installer --install-dir $env:LOCALAPPDATA\ComposerSetup --filename composer.exe
  $composer = "$env:LOCALAPPDATA\ComposerSetup\composer.exe"
  if (-not (Test-Path $composer)) { throw 'Composer installation failed' }
  $env:Path += ";$($env:LOCALAPPDATA)\ComposerSetup"
}

Ensure-Composer

New-Item -ItemType Directory -Force -Path $AppDir | Out-Null
Set-Location $AppDir

# Create Laravel project
composer create-project laravel/laravel . --no-interaction

# Require packages
composer require guzzlehttp/guzzle jenssegers/agent

# Env
Copy-Item .env.example .env -Force
php artisan key:generate

# SQLite
New-Item -ItemType Directory -Force -Path .\database | Out-Null
New-Item -ItemType File -Force -Path .\database\database.sqlite | Out-Null
(Get-Content .env) -replace '^DB_CONNECTION=.*','DB_CONNECTION=sqlite' | Set-Content .env
(Get-Content .env) -replace '^DB_DATABASE=.*','DB_DATABASE='+ (Resolve-Path .\database\database.sqlite) | Set-Content .env

# Apply overlay
$overlay = Join-Path $PSScriptRoot '..\overlay'
if (Test-Path $overlay) { Copy-Item -Recurse -Force "$overlay\*" . }

# Register StopBot middleware alias in Kernel
$kernel = Get-Content .\app\Http\Kernel.php -Raw
$kernel = $kernel -replace 'protected \$middlewareAliases = \[', "protected \$middlewareAliases = [`n        'stopbot' => App\\Http\\Middleware\\StopBotMiddleware::class,"
Set-Content .\app\Http\Kernel.php $kernel

# Migrate
php artisan migrate

# Remove tests as requested
Remove-Item -Recurse -Force .\tests

Write-Host "ShortV1 app ready at $AppDir"
