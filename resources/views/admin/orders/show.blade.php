@extends('layouts.admin')

@section('title', 'Szczegóły zamówienia')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h2 class="text-3xl font-bold mb-6">📦 Szczegóły zamówienia #{{ $order->id }}</h2>

    @php
        $statuses = [
            'unordered' => 'W koszyku',
            'in_progress' => 'W trakcie',
            'completed' => 'Zakończono',
            'cancelled' => 'Anulowano',
        ];
        $days = \Carbon\Carbon::parse($order->start_date)->diffInDays(\Carbon\Carbon::parse($order->end_date)) + 1;
    @endphp

    <div class="bg-white shadow-md p-6 rounded space-y-4">
        <h3 class="text-xl font-semibold mb-2">👤 Informacje o kliencie</h3>
        <p><strong>Imię i nazwisko:</strong> {{ $order->user->first_name }} {{ $order->user->last_name }}</p>
        <p><strong>Email:</strong> {{ $order->user->email }}</p>

        <h3 class="text-xl font-semibold mt-6 mb-2">🗓️ Okres realizacji</h3>
        <p><strong>Data rozpoczęcia:</strong> {{ $order->start_date->format('d.m.Y') }}</p>
        <p><strong>Data zakończenia:</strong> {{ $order->end_date->format('d.m.Y') }}</p>
        <p><strong>Liczba dni cateringu:</strong> {{ $days }}</p>
        <p><strong>Status:</strong> {{ $statuses[$order->status] ?? $order->status }}</p>

        <h3 class="text-xl font-semibold mt-6 mb-2">💰 Podsumowanie</h3>
        <p><strong>Cena całkowita:</strong> {{ number_format($order->total_price, 2, ',', ' ') }} zł</p>

        @if($order->discount_code)
            <p><strong>Kod rabatowy użyty:</strong> {{ $order->discount_code }}</p>
        @endif

        @if($order->cancellation)
            <p class="text-red-600"><strong>Powód anulowania:</strong> {{ $order->cancellation->reason }}</p>
            @if($order->cancellation->discount)
                <p><strong>Przyznany kod rabatowy:</strong> {{ $order->cancellation->discount->code }}
                    ({{ $order->cancellation->discount->value }}{{ $order->cancellation->discount->type === 'percent' ? '%' : ' zł' }})
                </p>
            @endif
        @endif

        <h3 class="text-xl font-semibold mt-6 mb-2">🏠 Adres dostawy</h3>
        <p><strong>Miasto:</strong> {{ $order->city }}</p>
        <p><strong>Kod pocztowy:</strong> {{ $order->postal_code }}</p>
        <p><strong>Ulica:</strong> {{ $order->street }}</p>
        <p><strong>Nr mieszkania / lokalu:</strong> {{ $order->apartment_number ?? '—' }}</p>
    </div>

    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4">🧾 Produkty w zamówieniu</h3>

        @foreach ($order->items as $item)
            @php
                $totalForItem = $item->unit_price * $item->quantity * $days;
            @endphp
            <div class="border rounded p-4 mb-4 bg-gray-50">
                <p><strong>Nazwa:</strong> {{ $item->product->name }}</p>
                <p><strong>Ilość:</strong> {{ $item->quantity }}</p>
                <p><strong>Cena jednostkowa:</strong> {{ number_format($item->unit_price, 2, ',', ' ') }} zł</p>
                <p><strong>Liczba dni:</strong> {{ $days }}</p>
                <p><strong>Łączna kwota za pozycję:</strong> {{ number_format($totalForItem, 2, ',', ' ') }} zł</p>
                <p><strong>Wegańskie:</strong> {{ $item->product->is_vegan ? 'Tak' : 'Nie' }}</p>
                <p><strong>Wegetariańskie:</strong> {{ $item->product->is_vegetarian ? 'Tak' : 'Nie' }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">← Powrót do listy</a>
        <a href="{{ route('admin.orders.edit', $order->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">✏️ Edytuj zamówienie</a>
    </div>
</div>
@endsection
