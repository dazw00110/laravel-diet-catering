@extends('layouts.client')

@section('title', 'Moje zamówienia')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded space-y-10">
    <h1 class="text-2xl font-bold mb-4">📦 Moje zamówienia</h1>

    {{-- 🔎 Filtrowanie i sortowanie --}}
    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded">
        <div>
            <label class="block text-sm font-medium">ID zamówienia</label>
            <input type="number" name="order_id" value="{{ request('order_id') }}" class="input input-bordered w-full" placeholder="np. 123">
        </div>
        <div>
            <label class="block text-sm font-medium">Status</label>
            <select name="status" class="input input-bordered w-full">
                <option value="">Wszystkie</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>W realizacji</option>
                <option value="completed" @selected(request('status') === 'completed')>Ukończone</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Przerwane</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Sortuj</label>
            <select name="sort" class="input input-bordered w-full">
                <option value="id_desc" @selected(request('sort', 'id_desc') === 'id_desc')>ID malejąco</option>
                <option value="id_asc" @selected(request('sort') === 'id_asc')>ID rosnąco</option>
                <option value="date_desc" @selected(request('sort') === 'date_desc')>Data malejąco</option>
                <option value="date_asc" @selected(request('sort') === 'date_asc')>Data rosnąco</option>
                <option value="total_desc" @selected(request('sort') === 'total_desc')>Kwota malejąco</option>
                <option value="total_asc" @selected(request('sort') === 'total_asc')>Kwota rosnąco</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-white">.</label>
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Filtruj</button>
        </div>
    </form>

    {{-- 🔁 Zamówienia w realizacji --}}
    <section>
        <h2 class="text-xl font-semibold mb-2">🕒 W realizacji</h2>

        @forelse ($activeOrders as $order)
            <div class="border p-4 rounded-xl mb-4 bg-gray-50 shadow-md">
                <div class="md:flex md:justify-between gap-6">
                    <div class="flex-1 space-y-2">
                        <p><strong>ID zamówienia:</strong> {{ $order->id }}</p>
                        <p><strong>Planowana data zakończenia:</strong> {{ $order->end_date->format('Y-m-d') }}</p>
                        <p class="text-gray-500 text-sm">
                            Liczba dni: {{ $order->start_date->diffInDays($order->end_date) + 1 }}
                        </p>
                        <p><strong>Status:</strong> <span class="text-yellow-600 font-semibold">⏳ W realizacji</span></p>
                        <p><strong>Adres dostawy:</strong>
                            {{ $order->city }}, {{ $order->street }} {{ $order->apartment_number }}, {{ $order->postal_code }}
                        </p>

                        <div>
                            <strong>Produkty:</strong>
                            <ul class="list-disc list-inside">
                                @foreach($order->items as $item)
                                    <li>{{ $item->product->name ?? 'Produkt nieznany' }} — ilość: {{ $item->quantity }}, cena: {{ number_format($item->unit_price * $item->quantity, 2) }} zł</li>
                                @endforeach
                            </ul>
                        </div>

                        <p><strong>Razem:</strong> {{ number_format($order->total_price, 2) }} zł</p>

                        @if($order->discount_code)
                            <p><strong>Kod rabatowy:</strong> {{ $order->discount_code }}</p>
                        @endif

                        {{-- Zastosowane zniżki/promocje --}}
                        <div class="mt-2">
                            <strong>Zastosowane zniżki/promocje:</strong>
                            <ul class="list-disc list-inside text-sm ml-4">
                                @php
                                    $days = max($order->start_date->diffInDays($order->end_date) + 1, 7);
                                    $totalBeforeDiscounts = 0;
                                    $freeAmount = 0;
                                    $bulkDiscountPercent = 0;
                                    $bulkDiscountAmount = 0;
                                    $promotionsApplied = [];
                                    $itemsCount = [];
                                    foreach ($order->items as $item) {
                                        $totalBeforeDiscounts += $item->unit_price * $item->quantity * $days;
                                        $itemsCount[$item->product_id] = ($itemsCount[$item->product_id] ?? 0) + $item->quantity;
                                    }
                                    foreach ($itemsCount as $productId => $qty) {
                                        if ($qty >= 5) {
                                            $freeSets = intdiv($qty, 5);
                                            $product = $order->items->firstWhere('product_id', $productId)->product;
                                            $productPrice = $product->price * $days;
                                            $freeAmount += $freeSets * $productPrice;
                                            $promotionsApplied[] = "4+1 gratis: {$freeSets} darmowych porcji produktu \"{$product->name}\"";
                                        }
                                    }
                                    $totalAfterFree = $totalBeforeDiscounts - $freeAmount;
                                    if ($totalAfterFree >= 3000) {
                                        $bulkDiscountPercent = 15;
                                    } elseif ($totalAfterFree >= 2000) {
                                        $bulkDiscountPercent = 10;
                                    }
                                    $bulkDiscountAmount = $totalAfterFree * ($bulkDiscountPercent / 100);
                                    if ($bulkDiscountPercent > 0) {
                                        $promotionsApplied[] = "Rabat {$bulkDiscountPercent}% od kwoty powyżej 2000 zł";
                                    }
                                @endphp
                                @if($freeAmount > 0)
                                    <li>Darmowe produkty (4+1): -{{ number_format($freeAmount, 2) }} zł</li>
                                @endif
                                @if($bulkDiscountPercent > 0)
                                    <li>Rabat {{ $bulkDiscountPercent }}%: -{{ number_format($bulkDiscountAmount, 2) }} zł</li>
                                @endif
                                @if($order->discount_code)
                                    <li>Kod rabatowy: {{ $order->discount_code }}</li>
                                @endif
                                @if(empty($promotionsApplied) && !$order->discount_code)
                                    <li>Brak dodatkowych zniżek</li>
                                @endif
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('client.orders.cancel', $order) }}" class="mt-2">
                            @csrf
                            <button onclick="return confirm('Na pewno przerwać to zamówienie?')" class="w-52 h-12 text-center bg-red-500 text-white rounded hover:bg-red-600 font-semibold">
                                Przerwij zamówienie
                            </button>
                        </form>
                    </div>

                    {{-- 🗓 Kalendarz --}}
                    @if($order->start_date && $order->end_date)
                        <div class="md:w-[260px] mt-6 md:mt-0"
                            x-data="calendarComponent(
                                '{{ $order->start_date->toDateString() }}',
                                '{{ $order->end_date->toDateString() }}',
                                '{{ $order->status }}',
                                '{{ $order->cancellation->cancellation_date ?? '' }}'
                            )"
                            x-init="init()">
                            <div class="border p-4 rounded bg-white shadow-md">
                                <div class="flex justify-between items-center mb-2">
                                    <button @click="prevMonth" class="text-xs px-2 py-1 border rounded hover:bg-gray-200">←</button>
                                    <h3 class="text-sm font-semibold text-gray-700 text-center"
                                        x-text="currentMonth.toLocaleDateString('pl-PL', { month: 'long', year: 'numeric' })"></h3>
                                    <button @click="nextMonth" class="text-xs px-2 py-1 border rounded hover:bg-gray-200">→</button>
                                </div>

                                <div class="grid grid-cols-7 text-[10px] text-center gap-0.5">
                                    <template x-for="day in days" :key="day.date">
                                        <div :class="day.classes" x-text="day.day"></div>
                                    </template>
                                </div>

                                <div class="text-[10px] text-gray-600 mt-3 flex flex-wrap justify-center gap-4">
                                    <div><span class="inline-block w-3 h-3 bg-green-500 rounded mr-1 align-middle"></span> Ukończono</div>
                                    <div><span class="inline-block w-3 h-3 bg-yellow-400 rounded mr-1 align-middle"></span> W trakcie</div>
                                    <div><span class="inline-block w-3 h-3 bg-red-400 rounded mr-1 align-middle"></span> Przerwany</div>
                                </div>
                            </div>
                        </div>
                    @endif
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
            <div class="border p-4 rounded-xl mb-4 bg-gray-100 shadow-md">
                <div class="md:flex md:justify-between gap-6">
                    <div class="flex-1 space-y-2">
                        <p><strong>ID:</strong> {{ $order->id }}</p>
                        <p><strong>Status:</strong> {{ $order->status === 'cancelled' ? '❌ Przerwane' : '✔️ Ukończone' }}</p>
                        <p><strong>Data rozpoczęcia:</strong> {{ $order->start_date->format('Y-m-d') }}</p>
                        <p><strong>Adres dostawy:</strong>
                            {{ $order->city }}, {{ $order->street }} {{ $order->apartment_number }}, {{ $order->postal_code }}
                        </p>
                        <p><strong>Planowana data zakończenia:</strong> {{ $order->end_date->format('Y-m-d') }}
                    <span class="text-gray-500 text-sm">
                        ({{ $order->start_date->diffInDays($order->end_date) + 1 }} dni)
                    </span>
                </p>
                        @if($order->status === 'cancelled' && $order->cancellation)
                            <p><strong>Data anulowania:</strong> {{ $order->cancellation->cancellation_date }}</p>
                            @if ($order->cancellation->discount)
                                <p class="text-green-700 font-semibold">
                                    🎁 Otrzymałeś rabat za niezadowolenie z cateringu:
                                    <span class="inline-block bg-green-600 text-white px-2 py-1 rounded">
                                        {{ $order->cancellation->discount->code }} ({{ $order->cancellation->discount->value }}{{ $order->cancellation->discount->type === 'percentage' ? '%' : 'zł' }})
                            </span>
                                </p>
                            @endif
                        @endif

                        <div>
                            <strong>Produkty:</strong>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($order->items as $item)
                                    <li>
                                        {{ $item->product->name ?? 'Produkt nieznany' }} — ilość: {{ $item->quantity }}, cena: {{ number_format($item->unit_price * $item->quantity, 2) }} zł
                                        @php
                                            $review = $item->product->reviews()->where('user_id', $order->user_id)->first();
                                        @endphp
                                        @if ($review)
                                            <div class="ml-2 text-sm text-yellow-500">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}">★</span>
                                                @endfor
                                                <p class="italic text-gray-600">{{ $review->comment ? '„' . $review->comment . '”' : 'Brak komentarza.' }}</p>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <p><strong>Razem:</strong> {{ number_format($order->total_price, 2) }} zł</p>

                        @if($order->discount_code)
                            <p><strong>Kod rabatowy:</strong> {{ $order->discount_code }}</p>
                        @endif

                        {{-- Zastosowane zniżki/promocje --}}
                        <div class="mt-2">
                            <strong>Zastosowane zniżki/promocje:</strong>
                            <ul class="list-disc list-inside text-sm ml-4">
                                @php
                                    $days = max($order->start_date->diffInDays($order->end_date) + 1, 7);
                                    $totalBeforeDiscounts = 0;
                                    $freeAmount = 0;
                                    $bulkDiscountPercent = 0;
                                    $bulkDiscountAmount = 0;
                                    $promotionsApplied = [];
                                    $itemsCount = [];
                                    foreach ($order->items as $item) {
                                        $totalBeforeDiscounts += $item->unit_price * $item->quantity * $days;
                                        $itemsCount[$item->product_id] = ($itemsCount[$item->product_id] ?? 0) + $item->quantity;
                                    }
                                    foreach ($itemsCount as $productId => $qty) {
                                        if ($qty >= 5) {
                                            $freeSets = intdiv($qty, 5);
                                            $product = $order->items->firstWhere('product_id', $productId)->product;
                                            $productPrice = $product->price * $days;
                                            $freeAmount += $freeSets * $productPrice;
                                            $promotionsApplied[] = "4+1 gratis: {$freeSets} darmowych porcji produktu \"{$product->name}\"";
                                        }
                                    }
                                    $totalAfterFree = $totalBeforeDiscounts - $freeAmount;
                                    if ($totalAfterFree >= 3000) {
                                        $bulkDiscountPercent = 15;
                                    } elseif ($totalAfterFree >= 2000) {
                                        $bulkDiscountPercent = 10;
                                    }
                                    $bulkDiscountAmount = $totalAfterFree * ($bulkDiscountPercent / 100);
                                    if ($bulkDiscountPercent > 0) {
                                        $promotionsApplied[] = "Rabat {$bulkDiscountPercent}% od kwoty powyżej 2000 zł";
                                    }
                                @endphp
                                @if($freeAmount > 0)
                                    <li>Darmowe produkty (4+1): -{{ number_format($freeAmount, 2) }} zł</li>
                                @endif
                                @if($bulkDiscountPercent > 0)
                                    <li>Rabat {{ $bulkDiscountPercent }}%: -{{ number_format($bulkDiscountAmount, 2) }} zł</li>
                                @endif
                                @if($order->discount_code)
                                    <li>Kod rabatowy: {{ $order->discount_code }}</li>
                                @endif
                                @if(empty($promotionsApplied) && !$order->discount_code)
                                    <li>Brak dodatkowych zniżek</li>
                                @endif
                            </ul>
                        </div>

                        {{-- 🟩 PRZYCISKI --}}
                        <div class="flex flex-wrap gap-3 pt-2">
                            <form action="{{ route('client.orders.repeat', $order) }}" method="POST">
                                @csrf
                                <button class="w-52 h-12 text-center bg-blue-500 text-white rounded hover:bg-blue-600 font-semibold">
                                    Zamów ponownie
                                </button>
                            </form>

                            @php
                                $hasUnrated = false;
                                foreach ($order->items as $item) {
                                    $product = $item->product;
                                    if ($product && !$product->reviews()->where('user_id', $order->user_id)->exists()) {
                                        $hasUnrated = true;
                                        break;
                                    }
                                }
                            @endphp

                            @if ($hasUnrated)
                                <a href="{{ route('client.orders.reviews.create', $order) }}"
                                   class="w-52 h-12 flex items-center justify-center text-center bg-yellow-500 text-white rounded hover:bg-yellow-600 font-semibold">
                                    Wystaw opinię
                                </a>
                            @else
                                <span class="w-52 h-12 flex items-center justify-center text-center bg-green-600 text-white rounded font-semibold">
                                    Wszystkie produkty ocenione
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 📅 Kalendarz --}}
                    @if($order->start_date && $order->end_date)
                        <div class="md:w-[260px] mt-6 md:mt-0"
                            x-data="calendarComponent(
                                '{{ $order->start_date->toDateString() }}',
                                '{{ $order->end_date->toDateString() }}',
                                '{{ $order->status }}',
                                '{{ ($order->status === 'cancelled' && $order->cancellation) ? $order->cancellation->cancellation_date : '' }}'
                            )"
                            x-init="init()">
                            <div class="border p-4 rounded bg-white shadow-md">
                                <div class="flex justify-between items-center mb-2">
                                    <button @click="prevMonth" class="text-xs px-2 py-1 border rounded hover:bg-gray-200">←</button>
                                    <h3 class="text-sm font-semibold text-gray-700 text-center"
                                        x-text="currentMonth.toLocaleDateString('pl-PL', { month: 'long', year: 'numeric' })"></h3>
                                    <button @click="nextMonth" class="text-xs px-2 py-1 border rounded hover:bg-gray-200">→</button>
                                </div>
                                <div class="grid grid-cols-7 text-[10px] text-center gap-0.5">
                                    <template x-for="day in days" :key="day.date">
                                        <div :class="day.classes" x-text="day.day"></div>
                                    </template>
                                </div>
                                <div class="text-[10px] text-gray-600 mt-3 flex flex-wrap justify-center gap-4">
                                    <div><span class="inline-block w-3 h-3 bg-green-500 rounded mr-1 align-middle"></span> Ukończono</div>
                                    <div><span class="inline-block w-3 h-3 bg-yellow-400 rounded mr-1 align-middle"></span> W trakcie</div>
                                    <div><span class="inline-block w-3 h-3 bg-red-400 rounded mr-1 align-middle"></span> Przerwany</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500">Brak ukończonych zamówień.</p>
        @endforelse
    </section>
</div>
@endsection
<script>
function calendarComponent(startDate, endDate, status, cancelDate = null) {
    const start = new Date(startDate + 'T00:00:00');
    const end = new Date(endDate + 'T00:00:00');
    const cancel = cancelDate ? new Date(cancelDate + 'T00:00:00') : null;

    return {
        currentMonth: new Date(start.getFullYear(), start.getMonth(), 1),
        days: [],
        init() {
            this.generateDays();
        },
        nextMonth() {
            this.currentMonth.setMonth(this.currentMonth.getMonth() + 1);
            this.currentMonth = new Date(this.currentMonth);
            this.generateDays();
        },
        prevMonth() {
            this.currentMonth.setMonth(this.currentMonth.getMonth() - 1);
            this.currentMonth = new Date(this.currentMonth);
            this.generateDays();
        },
        generateDays() {
            const monthStart = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), 1);
            const monthEnd = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 0);
            const startDayOfWeek = (monthStart.getDay() + 6) % 7;
            const totalCells = startDayOfWeek + monthEnd.getDate();

            const days = [];

            for (let i = 0; i < totalCells; i++) {
                const d = new Date(monthStart);
                d.setDate(i - startDayOfWeek);
                const dateStr = d.toLocaleDateString('sv-SE'); // YYYY-MM-DD

                let colorClass = 'bg-gray-100 text-gray-700';

                if (d >= start && d <= end && d.getMonth() === this.currentMonth.getMonth()) {
                    if (status === 'cancelled' && cancel && cancel >= start && cancel <= end) {
                        const cancelStr = cancel.toLocaleDateString('sv-SE');

                        if (dateStr === cancelStr) {
                            colorClass = 'bg-green-500 text-white'; // dzień anulowania
                        } else if (d < cancel) {
                            colorClass = 'bg-yellow-400 text-white'; // przed anulowaniem
                        } else {
                            colorClass = 'bg-red-400 text-white'; // po anulowaniu
                        }
                    } else if (status === 'in_progress') {
                        colorClass = 'bg-yellow-400 text-white';
                    } else if (status === 'completed') {
                        colorClass = 'bg-green-500 text-white';
                    }
                }

                days.push({
                    date: dateStr,
                    day: d.getMonth() === this.currentMonth.getMonth() ? d.getDate() : '',
                    classes: 'p-0.5 rounded text-center text-[10px] ' + colorClass,
                });
            }

            this.days = days;
        }

    };
}

</script>

