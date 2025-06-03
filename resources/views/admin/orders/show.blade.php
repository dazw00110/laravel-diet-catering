@extends('layouts.admin')

@section('title', 'Szczegóły zamówienia')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h2 class="text-3xl font-bold mb-6">Szczegóły zamówienia #{{ $order->id }}</h2>

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
        <p><strong>Klient:</strong> {{ $order->user->first_name }} {{ $order->user->last_name }}</p>
        <p><strong>Data rozpoczęcia:</strong> {{ $order->start_date }}</p>
        <p><strong>Data zakończenia:</strong> {{ $order->end_date }}</p>
        <p><strong>Status:</strong> {{ $statuses[$order->status] ?? $order->status }}</p>
        <p><strong>Cena całkowita:</strong> {{ number_format($order->total_price, 2) }} zł</p>

        @if($order->cancellation)
            <p class="text-red-600"><strong>Anulowano:</strong> {{ $order->cancellation->reason }}</p>
            @if($order->cancellation->discount)
                <p><strong>Kod rabatowy:</strong> {{ $order->cancellation->discount->code }}
                    ({{ $order->cancellation->discount->value }}{{ $order->cancellation->discount->type === 'percent' ? '%' : ' zł' }})
                </p>
            @endif
        @endif
    </div>

    <div class="mt-6">
        <h3 class="text-xl font-semibold mb-3">Produkty w zamówieniu:</h3>

        @foreach ($order->items as $item)
            @php
                $totalForItem = $item->unit_price * $item->quantity * $days;
            @endphp
            <div class="border rounded p-4 mb-4 bg-gray-50">
                <p><strong>Nazwa:</strong> {{ $item->product->name }}</p>
                <p><strong>Ilość:</strong> {{ $item->quantity }}</p>
                <p><strong>Cena jednostkowa:</strong> {{ number_format($item->unit_price, 2) }} zł</p>
                <p><strong>Liczba dni:</strong> {{ $days }}</p>
                <p><strong>Łącznie za pozycję:</strong> {{ number_format($totalForItem, 2) }} zł</p>
                <p><strong>Wegańskie:</strong> {{ $item->product->is_vegan ? 'Tak' : 'Nie' }}</p>
                <p><strong>Wegetariańskie:</strong> {{ $item->product->is_vegetarian ? 'Tak' : 'Nie' }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">← Powrót</a>
        <a href="{{ route('admin.orders.edit', $order->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edytuj zamówienie</a>
    </div>
</div>
@endsection
