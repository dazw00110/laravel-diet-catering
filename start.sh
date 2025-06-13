#!/bin/bash
# Laravel Diet Catering - Setup (No Docker Required)

echo "==============================================="
echo "Laravel Setup Script (No Docker Required)"
echo "==============================================="

# Tworzenie pliku .env, jeśli nie istnieje
if [ ! -f .env ]; then
    echo "Tworzenie pliku .env z .env.example..."
    cp .env.example .env
fi

# Tworzenie folderów tymczasowych
echo "Tworzenie folderów cache, logs..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# Instalowanie zależności PHP
echo "Instalowanie zależności Composer..."
if [ -f composer.phar ]; then
    php composer.phar install
else
    composer install
fi

# Sprawdzenie czy instalacja composer zakończyła się sukcesem
if [ ! -f vendor/autoload.php ]; then
    echo "❌ Błąd: composer install nie powiódł się."
    exit 1
fi

# Generowanie klucza aplikacji
echo "Generowanie klucza aplikacji..."
php artisan key:generate

# Tworzenie symbolicznego linku do storage/public
echo "Tworzenie linku storage/public..."
php artisan storage:link

# Migracje i seedy
echo "Migracje i seedy..."
php artisan migrate:fresh --seed

# Instalacja zależności NPM
if [ -f package.json ]; then
    echo "Instalowanie paczek NPM..."
    npm install
else
    echo "Brak package.json — pomijam npm install."
fi

# Uruchamianie Vite i serwera Laravel w osobnych terminalach
echo "Uruchamianie Vite (npm run dev)..."
gnome-terminal -- bash -c "npm run dev; exec bash"

echo "Uruchamianie serwera Laravel (php artisan serve)..."
gnome-terminal -- bash -c "php artisan serve; exec bash"

echo "==============================================="
echo "✅ Wszystko gotowe! Odwiedź: http://127.0.0.1:8000"
echo "==============================================="
