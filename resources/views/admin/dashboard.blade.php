@extends('layouts.admin')

@section('title', 'Panel administratora')

@section('content')
    @if(app()->environment('local') && auth()->user()->totp_secret)
        <form method="POST" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Na pewno chcesz wyłączyć TOTP?')">
            @csrf
            <button class="mb-6 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                Wyłącz uwierzytelnianie 2FA
            </button>
        </form>
    @endif

    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-800">🛠️ Panel administratora</h1>
        <p class="text-gray-600 mt-2">Zarządzaj użytkownikami, zamówieniami, produktami i analizuj dane</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Users -->
        <a href="{{ route('admin.users.index') }}" class="group bg-gradient-to-br from-rose-100 to-rose-200 p-6 rounded-2xl shadow hover:scale-105 transition">
            <div class="text-4xl mb-2">👥</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Użytkownicy</h2>
            <p class="text-sm text-gray-700">Zarządzaj kontami użytkowników.</p>
        </a>

        <!-- Orders -->
        <a href="{{ route('admin.orders.index') }}" class="group bg-gradient-to-br from-blue-100 to-blue-200 p-6 rounded-2xl shadow hover:scale-105 transition">
            <div class="text-4xl mb-2">📦</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Zamówienia</h2>
            <p class="text-sm text-gray-700">Przeglądaj i edytuj zamówienia.</p>
        </a>

        <!-- Products -->
        <a href="{{ route('admin.products.index') }}" class="group bg-gradient-to-br from-green-100 to-green-200 p-6 rounded-2xl shadow hover:scale-105 transition">
            <div class="text-4xl mb-2">🥗</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Produkty</h2>
            <p class="text-sm text-gray-700">Aktualizuj oferty cateringowe.</p>
        </a>

        <!-- Statistics -->
        <a href="{{ route('admin.stats.index') }}" class="group bg-gradient-to-br from-purple-100 to-purple-200 p-6 rounded-2xl shadow hover:scale-105 transition">
            <div class="text-4xl mb-2">📊</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Statystyki</h2>
            <p class="text-sm text-gray-700">Podgląd danych i analiz.</p>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-br from-yellow-100 to-yellow-200 p-6 rounded-2xl shadow hover:scale-105 transition">
            <div class="text-4xl mb-2">👤</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Mój profil</h2>
            <p class="text-sm text-gray-700">Zarządzaj swoimi danymi.</p>
        </a>
    </div>
@endsection
