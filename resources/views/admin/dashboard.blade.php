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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Users -->
        <a href="{{ route('admin.users.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Użytkownicy</h2>
            <p class="text-sm text-gray-600">Zarządzaj kontami użytkowników.</p>
        </a>

        <!-- Orders -->
        <a href="{{ route('admin.orders.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Zamówienia</h2>
            <p class="text-sm text-gray-600">Przeglądaj i edytuj zamówienia.</p>
        </a>

        <!-- Products -->
        <a href="{{ route('admin.products.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Produkty</h2>
            <p class="text-sm text-gray-600">Aktualizuj oferty cateringowe.</p>
        </a>

        <!-- Statistics -->
        <a href="{{ route('admin.stats.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Statystyki</h2>
            <p class="text-sm text-gray-600">Podgląd danych i analiz.</p>
        </a>

        <!-- Profile -->
        <a href="{{ route('profile.edit') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Mój profil</h2>
            <p class="text-sm text-gray-600">Zarządzaj swoimi danymi.</p>
        </a>
    </div>
@endsection
