@extends('layouts.client')

@section('title', 'Moje zamówienia')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded space-y-10">
    <h1 class="text-2xl font-bold mb-4">📦 Moje zamówienia</h1>

    {{-- 🔄 Zamówienia w realizacji --}}
    <section>
        <h2 class="text-xl font-semibold mb-2">🕒 W realizacji</h2>

        @forelse ($activeOrders as $order)
            <div class="border p-4 rounded mb-4 bg-gray-50">
                <div class="md:flex md:justify-between gap-6">
                    {{-- 📄 Szczegóły zamówienia --}}
                    <div class="flex-1">
                        <p><strong>ID zamówienia:</strong> {{ $order->id }}</p>
                        <p><strong>Okres:</strong> {{ $order->start_date->format('Y-m-d') }} - {{ $order->end_date->format('Y-m-d') }}</p>
                        <p><strong>Status:</strong>
                            @if($order->status === 'in_progress')
                                <span class="text-yellow-600 font-semibold">⏳ W realizacji</span>
                            @elseif($order->status === 'cancelled')
                                <span class="text-red-600 font-semibold">❌ Przerwane</span>
                            @else
                                <span class="text-green-600 font-semibold">✔️ {{ ucfirst($order->status) }}</span>
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

                    {{-- 📅 Kalendarz --}}
                    <div class="w-full md:w-64 mt-6 md:mt-0"
                        x-data="calendarComponent(
                            '{{ $order->start_date->toDateString() }}',
                            '{{ $order->end_date->toDateString() }}',
                            '{{ $order->status }}'
                        )">
                        <div class="border p-3 rounded bg-white shadow">
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">
                                📅 Okres: {{ $order->start_date->format('Y-m-d') }} – {{ $order->end_date->format('Y-m-d') }}
                            </h3>
                            <div class="grid grid-cols-7 text-xs text-center gap-1">
                                <template x-for="day in days" :key="day.date">
                                    <div :class="day.classes" x-text="day.day"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500">Brak aktywnych zamówień.</p>
        @endforelse
    </section>

    {{-- ✅ Ukończone i anulowane --}}
    <section>
        <h2 class="text-xl font-semibold mb-2">✅ Ukończone i przerwane</h2>

        @forelse ($completedOrders as $order)
            <div class="border p-4 rounded mb-4 bg-gray-100">
                <div class="md:flex md:justify-between gap-6">
                    {{-- 📄 Szczegóły --}}
                    <div class="flex-1">
                        <p><strong>ID:</strong> {{ $order->id }}</p>
                        <p><strong>Status:</strong>
                            {{ $order->status === 'cancelled' ? '❌ Przerwane' : '✔️ Ukończone' }}
                        </p>
                        <p><strong>Data rozpoczęcia:</strong> {{ $order->start_date->format('Y-m-d') }}</p>
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

                    {{-- 📅 Kalendarz --}}
                    <div class="w-full md:w-64 mt-6 md:mt-0"
                        x-data="calendarComponent(
                            '{{ $order->start_date->toDateString() }}',
                            '{{ $order->end_date->toDateString() }}',
                            '{{ $order->status }}'
                        )">
                        <div class="border p-3 rounded bg-white shadow">
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">
                                📅 Okres: {{ $order->start_date->format('Y-m-d') }} – {{ $order->end_date->format('Y-m-d') }}
                            </h3>
                            <div class="grid grid-cols-7 text-xs text-center gap-1">
                                <template x-for="day in days" :key="day.date">
                                    <div :class="day.classes" x-text="day.day"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500">Brak ukończonych zamówień.</p>
        @endforelse
    </section>
</div>
@endsection

{{-- 🔧 Alpine.js – Komponent kalendarza --}}
<script>
    function calendarComponent(startDate, endDate, status) {
        const today = new Date().toISOString().split('T')[0];
        const start = new Date(startDate);
        const end = new Date(endDate);
        const displayStart = new Date(start);
        displayStart.setDate(displayStart.getDate() - 7);
        const displayEnd = new Date(end);
        displayEnd.setDate(displayEnd.getDate() + 14);

        let days = [];
        for (let d = new Date(displayStart); d <= displayEnd; d.setDate(d.getDate() + 1)) {
            const dateStr = d.toISOString().split('T')[0];
            let colorClass = 'bg-gray-100 text-gray-700';

            if (dateStr === today) {
                colorClass = 'bg-yellow-500 text-white font-bold';
            } else if (dateStr >= startDate && dateStr <= endDate) {
                colorClass =
                    status === 'cancelled' ? 'bg-red-400 text-white' :
                    status === 'in_progress' ? 'bg-yellow-400 text-white' :
                    'bg-green-500 text-white';
            }

            days.push({
                date: dateStr,
                day: d.getDate(),
                classes: 'p-1 rounded ' + colorClass
            });
        }

        return { days };
    }
</script>
