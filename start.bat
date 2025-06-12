@echo off
title Laravel Diet Catering - Setup

echo ===============================================
echo Laravel Setup Script
echo ===============================================

REM Tworzenie pliku .env jeśli nie istnieje
IF NOT EXIST .env (
    echo Tworzenie pliku .env z .env.example...
    copy .env.example .env
)

REM Tworzenie wymaganych folderów
echo Tworzenie folderow tymczasowych...
mkdir bootstrap\cache >nul 2>nul
mkdir storage\framework\cache >nul 2>nul
mkdir storage\framework\sessions >nul 2>nul
mkdir storage\framework\views >nul 2>nul
mkdir storage\logs >nul 2>nul

REM Instalacja zależności PHP
echo Instalowanie zaleznosci PHP...
IF EXIST composer.phar (
    call php composer.phar install
) ELSE (
    call composer install
)

IF NOT EXIST vendor\autoload.php (
    echo Blad: composer install nie powiodl sie.
    pause
    exit /b
)

REM Linkowanie storage (ważne!)
echo Tworzenie linku storage...
call php artisan storage:link

REM Generowanie klucza
echo Generowanie klucza aplikacji...
call php artisan key:generate

REM Migracje + seedy
echo Migracje i seedy...
call php artisan migrate:fresh --seed

REM Instalacja npm
IF EXIST package.json (
    echo Instalacja paczek NPM...
    call npm install
) ELSE (
    echo Brak package.json — pomijam npm install.
)

REM Uruchomienie serwerów
echo Uruchamianie Vite i backendu Laravel...
start cmd /k "npm run dev"
start cmd /k "php artisan serve"

echo ===============================================
echo Wszystko gotowe! Odwiedz: http://127.0.0.1:8000
echo ===============================================
pause
