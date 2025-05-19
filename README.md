# Projekt AI1

[Repozytorium projektu](https://github.com/dazw00110/laravel-diet-catering)

[Tablica projektowa](https://github.com/users/dazw00110/projects/3)

---


### Temat projektu

Zamawianie cateringu dietetycznego (dieta pudełkowa) 

---

### Zespół X

| Profil | Imię i nazwisko | Rola |
| ------ | ---------------- | ----- |
| [dazw00110](https://github.com/dazw00110/Automated-Discretization-Framework) | Damian Zwolak | lider zespołu |
| [Shakalito](https://github.com/Shakalito) | Jakub Strzępek | członek zespołu |
| [Kriisowy](https://github.com/Kriisowy) | Krystian Zygmunt | członek zespołu |


---


## Opis projektu

Projekt zakłada stworzenie platformy do zamawiania cateringu dietetycznego. Aplikacja umożliwia użytkownikom przeglądanie ofert cateringu, składanie zamówień, zarządzanie koszykiem oraz historią zamówień. Dodatkowo dostępne są panele administracyjne i pracownicze do obsługi systemu, tworzenia promocji i zarządzania cateringiem.

Dostępne funkcjonalności:
- rejestracja i logowanie użytkowników (z podziałem na role: klient, 
- pracownik, administrator),
- przeglądanie ofert cateringu z filtrowaniem i paginacją,
- dodawanie produktów do koszyka i składanie zamówienia,
- zarządzanie historią zamówień i harmonogramem dostaw,
- opinie klientów oraz ocena cateringu,
- obsługa kodów rabatowych i promocji last minute,
- panel admina: zarządzanie użytkownikami i ofertami,
- panel pracownika: statystyki sprzedaży i zarządzanie cateringami,
- system lojalnościowy i przypomnienia o kończących się zamówieniach,
- reset hasła z TOTP i obsługa błędów (403, 404, 500).



### Narzędzia i technologie
- PHP 8.3
- Laravel 12.x
- PostgreSQL 15 (Docker)
- Composer
- Node.js + Vite (do budowania frontu)
- Blade (Laravel template engine)
- Faker (generowanie danych testowych)
- Git + GitHub (zarządzanie kodem)
- Docker (kontener z bazą danych)
- Laravel Breeze (autoryzacja)

### Uruchomienie aplikacji

Aby uruchomić projekt lokalnie, należy mieć zainstalowane:

- PHP 8.2+
- Composer
- Laravel 12.x
- Node.js + npm (jeśli używasz Vite do frontendu)
- Baza danych (np. PostgreSQL lub MySQL — zależnie od .env)
- Opcjonalnie: Docker – jeśli używasz kontenera na bazę danych

Przykładowi użytkownicy aplikacji:
* administrator: jan@email.com 1234
* użytkownik: anna@email.com 1234
* ...
* ...

### Baza danych

![Diagram ERD](./docs-img/erd.png)

## Widoki aplikacji 

![Strona główna](./docs-img/screen.png)
*Strona główna*

![Strona główna](./docs-img/screen.png)
*Logowanie*

![Strona główna](./docs-img/screen.png)
*Rejestracja*

...

*CRUD*

...

*Zarządzanie użytkownikami*

...

*Profil użytkownika*

...

*Dokonanie zakupu/wypożyczenia...*

...

itd.

...


...
