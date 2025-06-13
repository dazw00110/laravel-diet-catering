@echo off
title Laravel Diet Catering - Setup (bez Dockera)

echo ===============================================
echo Laravel Setup Script (No Docker Required)
echo ===============================================

REM Tworzenie pliku .env, jeśli nie istnieje
IF NOT EXIST .env (
    echo Tworzenie pliku .env z .env.example...
    copy .env.example .env
)

REM Tworzenie folderów tymczasowych
echo Tworzenie folderów cache, logs...
mkdir bootstrap\cache >nul 2>nul
mkdir storage\framework\cache >nul 2>nul
mkdir storage\framework\sessions >nul 2>nul
mkdir storage\framework\views >nul 2>nul
mkdir storage\logs >nul 2>nul

REM Instalowanie zależności PHP
echo Instalowanie zależności Composer...
IF EXIST composer.phar (
    call php composer.phar install
) ELSE (
    call composer install
)

IF NOT EXIST vendor\autoload.php (
    echo ❌ Blad: composer install nie powiodl sie.
    pause
    exit /b
)

REM Generowanie klucza aplikacji
echo Generowanie klucza aplikacji...
call php artisan key:generate

REM Tworzenie symbolic link do storage/public
echo Tworzenie linku storage/public...
call php artisan storage:link

REM Wykonywanie migracji i seed'ów
echo Migracje i seedy...
call php artisan migrate:fresh --seed

REM Instalacja zależności npm
IF EXIST package.json (
    echo Instalowanie paczek NPM...
    call npm install
) ELSE (
    echo Brak package.json — pomijam npm install.
)

REM Uruchomienie Vite (npm run dev)
echo Uruchamianie Vite (npm run dev)...
start cmd /k "npm run dev"

REM Uruchomienie serwera Laravel
echo Uruchamianie serwera Laravel (php artisan serve)...
start cmd /k "php artisan serve"

echo ===============================================
echo ✅ Wszystko gotowe! Odwiedz: http://127.0.0.1:8000
echo ===============================================
pause
