@extends('layouts.client')

@section('title', 'Panel klienta')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('client.orders.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Moje zamówienia</h2>
            <p class="text-sm text-gray-600">Sprawdź historię i status zamówień.</p>
        </a>
        <a href="{{ route('client.products.index') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Produkty</h2>
            <p class="text-sm text-gray-600">Przeglądaj dostępne cateringi.</p>
        </a>
        <a href="{{ route('profile.edit') }}" class="bg-white p-6 rounded-xl shadow hover:bg-gray-100 transition">
            <h2 class="text-lg font-semibold mb-2">Mój profil</h2>
            <p class="text-sm text-gray-600">Edytuj swoje dane osobowe.</p>
        </a>
    </div>
@endsection
