@echo off
title Laravel Diet Catering - Setup

echo ===============================================
echo Laravel Setup Script
echo ===============================================

REM
IF NOT EXIST .env (
    echo Tworzenie pliku .env z .env.example...
    copy .env.example .env
)

REM
echo Tworzenie folderow tymczasowych...
mkdir bootstrap\cache >nul 2>nul
mkdir storage\framework\cache >nul 2>nul
mkdir storage\framework\sessions >nul 2>nul
mkdir storage\framework\views >nul 2>nul
mkdir storage\logs >nul 2>nul

REM
echo Instalowanie zaleznosci PHP...
IF EXIST composer.phar (
    call php composer.phar install
) ELSE (
    call composer install
)

REM
IF NOT EXIST vendor\autoload.php (
    echo Blad: composer install nie powiodl sie.
    pause
    exit /b
)

REM
echo Generowanie klucza aplikacji...
call php artisan key:generate

REM
echo Migracje i seedy...
call php artisan migrate:fresh --seed

REM
echo Instalacja paczek NPM...
IF EXIST package.json (
    call npm install
) ELSE (
    echo Brak package.json — pomijam npm install.
)

REM
echo Uruchamianie Vite i backendu Laravel...
start cmd /k "npm run dev"
start cmd /k "php artisan serve"

echo ===============================================
echo Wszystko gotowe! Odwiedz: http://127.0.0.1:8000
echo ===============================================
pause