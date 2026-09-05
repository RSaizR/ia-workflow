#!/bin/bash

set -e

cd /var/www/html

echo "========================================"
echo " Iniciando Laravel"
echo "========================================"


# Crear Laravel si no existe
if [ ! -f artisan ]; then

    echo "Laravel no encontrado. Creando proyecto..."

    composer create-project laravel/laravel . "^12.0"

fi


# Composer
composer install \
    --no-interaction \
    --prefer-dist


# Crear .env Laravel
if [ ! -f .env ]; then
    cp .env.example .env
fi


# Configurar BBDD
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/" .env
sed -i "s/^#\?DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^#\?DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -i "s/^#\?DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^#\?DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/^#\?DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env


# APP_KEY
if ! grep -qE '^APP_KEY=base64:.+' .env; then
    php artisan key:generate
fi


# Filament
if ! composer show filament/filament >/dev/null 2>&1; then

    echo "Instalando Filament..."

    composer require filament/filament:"^5.0" -W

    php artisan filament:install --panels

fi


# NPM
if [ -f package.json ]; then

    npm install
    npm run build

fi


# Migraciones
php artisan migrate --force


# Limpiar cachés
php artisan optimize:clear


echo ""
echo "========================================"
echo " Laravel listo"
echo " http://localhost:8000"
echo "========================================"
echo ""


exec php artisan serve \
    --host=0.0.0.0 \
    --port=8000