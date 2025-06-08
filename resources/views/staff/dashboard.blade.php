@extends('layouts.staff')

@section('title', 'Panel pracownika')

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
        <h1 class="text-4xl font-extrabold text-gray-800">👨‍🍳 Witaj w panelu pracownika</h1>
        <p class="text-gray-600 mt-2">Zarządzaj zamówieniami, analizuj sprzedaż i edytuj oferty cateringowe</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('staff.orders.index') }}" class="group bg-gradient-to-tr from-blue-100 to-blue-200 p-6 rounded-2xl shadow hover:scale-105 transition duration-200">
            <div class="text-4xl mb-3">📦</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Zamówienia</h2>
            <p class="text-sm text-gray-700">Zarządzaj realizowanymi zamówieniami klientów.</p>
        </a>

        <a href="{{ route('staff.products.index') }}" class="group bg-gradient-to-tr from-green-100 to-green-200 p-6 rounded-2xl shadow hover:scale-105 transition duration-200">
            <div class="text-4xl mb-3">🍱</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Produkty</h2>
            <p class="text-sm text-gray-700">Aktualizuj dostępne oferty cateringowe.</p>
        </a>

        <a href="{{ route('staff.stats.index') }}" class="group bg-gradient-to-tr from-purple-100 to-purple-200 p-6 rounded-2xl shadow hover:scale-105 transition duration-200">
            <div class="text-4xl mb-3">📊</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Statystyki</h2>
            <p class="text-sm text-gray-700">Analizuj dane sprzedaży i wyniki cateringu.</p>
        </a>



        <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-tr from-yellow-100 to-yellow-200 p-6 rounded-2xl shadow hover:scale-105 transition duration-200">
            <div class="text-4xl mb-3">👤</div>
            <h2 class="text-lg font-semibold text-gray-800 group-hover:underline">Mój profil</h2>
            <p class="text-sm text-gray-700">Zarządzaj swoim kontem i ustawieniami.</p>
        </a>
    </div>
@endsection
