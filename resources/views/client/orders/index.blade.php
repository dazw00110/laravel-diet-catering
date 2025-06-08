@extends('layouts.client')

@section('title', 'Moje zamówienia')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded space-y-10">

    <h1 class="text-2xl font-bold mb-4">📦 Moje zamówienia</h1>

    <section>
        <h2 class="text-xl font-semibold mb-2">🕒 W realizacji</h2>

        @forelse ($activeOrders as $order)
            <div class="border p-4 rounded mb-4 bg-gray-50">
                <p><strong>ID zamówienia:</strong> {{ $order->id }}</p>
                <p><strong>Okres:</strong> {{ $order->start_date->format('Y-m-d') }} - {{ $order->end_date->format('Y-m-d') }}</p>
                <p><strong>Status:</strong>
                    @if($order->status === 'in_progress')
                        ⏳ W realizacji
                    @elseif($order->status === 'cancelled')
                        ❌ Przerwane
                    @else
                        {{ ucfirst($order->status) }}
                    @endif
                </p>

                <div class="mt-2">
                    <strong>Produkty:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($order->items as $item)
                            <li>{{ $item->product->name ?? 'Produkt nieznany' }} — ilość: {{ $item->quantity }}, cena: {{ number_format($item->unit_price * $item->quantity, 2) }} zł</li>
                        @endforeach
                    </ul>
                </div>
                <p class="mt-2"><strong>Razem:</strong> {{ number_format($order->total_price, 2) }} zł</p>

                <div class="mt-2">
                    <strong>Adres dostawy:</strong>
                    <p>
                        {{ $order->city }}, {{ $order->postal_code }}<br>
                        {{ $order->street }} {{ $order->apartment_number }}
                    </p>
                </div>

                @if(!empty($order->discount_code))
                    <p><strong>Kod rabatowy:</strong> {{ $order->discount_code }}</p>
                @endif

                <form method="POST" action="{{ route('client.orders.cancel', $order) }}" class="mt-3">
                    @csrf
                    <button onclick="return confirm('Na pewno przerwać to zamówienie?')" class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600">
                        Przerwij zamówienie
                    </button>
                </form>
            </div>
        @empty
            <p class="text-gray-500">Brak aktywnych zamówień.</p>
        @endforelse
    </section>

    <section>
        <h2 class="text-xl font-semibold mb-2">✅ Ukończone i przerwane</h2>

        @forelse ($completedOrders as $order)
            <div class="border p-4 rounded mb-4 bg-gray-100">
                <p><strong>ID:</strong> {{ $order->id }}</p>
                <p><strong>Status:</strong>
                    {{ $order->status === 'cancelled' ? '❌ Przerwane' : '✔️ Ukończone' }}
                </p>
                <p><strong>Data zakończenia:</strong> {{ $order->end_date->format('Y-m-d') }}</p>

                <div class="mt-2">
                    <strong>Produkty:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($order->items as $item)
                            <li>{{ $item->product->name ?? 'Produkt nieznany' }} — ilość: {{ $item->quantity }}, cena: {{ number_format($item->unit_price * $item->quantity, 2) }} zł</li>
                        @endforeach
                    </ul>
                </div>
                <p class="mt-2"><strong>Razem:</strong> {{ number_format($order->total_price, 2) }} zł</p>

                <div class="mt-2">
                    <strong>Adres dostawy:</strong>
                    <p>
                        {{ $order->city }}, {{ $order->postal_code }}<br>
                        {{ $order->street }} {{ $order->apartment_number }}
                    </p>
                </div>

                @if(!empty($order->discount_code))
                    <p><strong>Kod rabatowy:</strong> {{ $order->discount_code }}</p>
                @endif

                <div class="flex gap-4 mt-3">
    <form method="POST" action="{{ route('client.cart.repeatOrder', $order) }}">
        @csrf
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
            Zamów ponownie
        </button>
    </form>

    @if (!$order->hasReviews())
    <a href="{{ route('client.orders.reviews.create', $order) }}" class="bg-yellow-500 text-white px-4 py-1 rounded hover:bg-yellow-600">
        Wystaw opinię
    </a>
@else
    <span class="text-green-600 font-medium">✔️ Opinia wystawiona</span>
@endif
</div>
            </div>
        @empty
            <p class="text-gray-500">Brak ukończonych zamówień.</p>
        @endforelse
    </section>
</div>
@endsection
