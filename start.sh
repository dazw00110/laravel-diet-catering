#!/bin/bash
set -e

echo "==============================================="
echo "Laravel Setup Script"
echo "==============================================="

if [ ! -f .env ]; then
    echo "Tworzenie pliku .env z .env.example..."
    cp .env.example .env
fi

echo "Tworzenie folderow tymczasowych..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

echo "Instalowanie zaleznosci PHP..."
if [ -f composer.phar ]; then
    php composer.phar install
else
    composer install
fi

if [ ! -f vendor/autoload.php ]; then
    echo "Blad: composer install nie powiodl sie."
    exit 1
fi

echo "Generowanie klucza aplikacji..."
php artisan key:generate

echo "Migracje i seedy..."
php artisan migrate:fresh --seed

if [ -f package.json ]; then
    echo "Instalacja paczek NPM..."
    npm install
else
    echo "Brak package.json — pomijam npm install."
fi

echo "Uruchamianie Vite i backendu Laravel..."
gnome-terminal -- npm run dev &
gnome-terminal -- php artisan serve &

echo "==============================================="
echo "Wszystko gotowe! Odwiedz: http://127.0.0.1:8000"
echo "==============================================="

read -p "Nacisnij ENTER aby zakonczyc..."