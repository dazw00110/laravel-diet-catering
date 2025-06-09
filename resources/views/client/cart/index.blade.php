@extends('layouts.client')

@section('title', 'Twój koszyk')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded space-y-8" x-data="cartData()">
    <h1 class="text-2xl font-bold mb-4">🍯 Twój koszyk</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($cart->items->isEmpty())
        <p class="text-gray-600">Koszyk jest pusty.</p>
    @else
        @php
            $days = max($cart->start_date->diffInDays($cart->end_date) + 1, 7);
            $totalBeforeDiscounts = 0;
            $promotionsApplied = [];

            foreach ($cart->items as $item) {
                $totalBeforeDiscounts += $item->unit_price * $item->quantity * $days;
            }

            $itemsCount = [];
            $freeAmount = 0;
            foreach ($cart->items as $item) {
                $itemsCount[$item->product_id] = ($itemsCount[$item->product_id] ?? 0) + $item->quantity;
            }
            foreach ($itemsCount as $productId => $qty) {
                if ($qty >= 5) {
                    $freeSets = intdiv($qty, 5);
                    $product = $cart->items->firstWhere('product_id', $productId)->product;
                    $productPrice = $product->price * $days;
                    $freeAmount += $freeSets * $productPrice;
                    $promotionsApplied[] = "4+1 gratis: {$freeSets} darmowych porcji produktu \"{$product->name}\"";
                }
            }

            $totalAfterFree = $totalBeforeDiscounts - $freeAmount;

            $bulkDiscountPercent = 0;
            if ($totalAfterFree >= 3000) {
                $bulkDiscountPercent = 15;
            } elseif ($totalAfterFree >= 2000) {
                $bulkDiscountPercent = 10;
            }
            $bulkDiscountAmount = $totalAfterFree * ($bulkDiscountPercent / 100);
            if ($bulkDiscountPercent > 0) {
                $promotionsApplied[] = "Rabat {$bulkDiscountPercent}% od kwoty powyżej 2000 zł";
            }

            $totalAfterBulk = $totalAfterFree - $bulkDiscountAmount;
        @endphp

        <!-- Tabela produktów -->
        <table class="w-full table-auto border-collapse mb-4">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Produkt</th>
                    <th class="px-4 py-2">Ilość</th>
                    <th class="px-4 py-2">Cena (1 dzień)</th>
                    <th class="px-4 py-2">Szczegóły</th>
                    <th class="px-4 py-2">Usuń</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart->items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('client.cart.update') }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="decrease" value="{{ $item->id }}" class="px-2 bg-gray-200 rounded">&minus;</button>
                                </form>
                                <span>{{ $item->quantity }}</span>
                                <form method="POST" action="{{ route('client.cart.update') }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="increase" value="{{ $item->id }}" class="px-2 bg-gray-200 rounded">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="px-4 py-2">{{ number_format($item->unit_price, 2) }} zł</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $item->unit_price }} zł × {{ $item->quantity }} × {{ $days }} dni =
                            <strong>{{ number_format($item->unit_price * $item->quantity * $days, 2) }} zł</strong>
                        </td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('client.cart.remove', $item) }}">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Na pewno usunąć ten produkt?')" class="text-red-600 hover:underline">❌</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Formularz dat -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            <form method="POST" action="{{ route('client.cart.updateDates') }}" class="space-y-4" x-data x-on:change.debounce.500ms="$el.submit()">
                @csrf
                @method('PATCH')

                <div>
                    <label for="start_date" class="block font-semibold mb-1">Data rozpoczęcia:</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ $cart->start_date->format('Y-m-d') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        max="{{ now()->addYear()->format('Y-m-d') }}"
                        class="input input-bordered w-auto"
                        x-data
                        x-on:change="
                            let start = $event.target.value;
                            let endInput = document.getElementById('end_date');
                            let minEnd = new Date(start);
                            minEnd.setDate(minEnd.getDate() + 7);
                            let minDateStr = minEnd.toISOString().split('T')[0];
                            endInput.min = minDateStr;
                            if(endInput.value < minDateStr) {
                                endInput.value = minDateStr;
                            }
                        "
                    >
                </div>

                <div>
                    <label for="end_date" class="block font-semibold mb-1">Data zakończenia:</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ $cart->end_date->format('Y-m-d') }}"
                        min="{{ $cart->start_date->copy()->addDays(7)->format('Y-m-d') }}"
                        max="{{ now()->addYear()->format('Y-m-d') }}"
                        class="input input-bordered w-auto"
                    >
                </div>
            </form>


            <!-- Formularz zamówienia -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('client.orders.store') }}" class="space-y-4" id="orderForm">
                    @csrf
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <label for="discount_code" class="block font-semibold w-full max-w-xs">Kod rabatowy:</label>
                        <input
                            type="text"
                            id="discount_code"
                            name="discount_code"
                            placeholder="np. RABAT10"
                            class="input input-bordered flex-grow max-w-xs"
                            x-model="discountCode"
                        />
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="applyDiscount"
                                class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 transition"
                            >
                                Aktywuj kod
                            </button>
                            <button
                                type="button"
                                @click="removeDiscount"
                                class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 transition"
                            >
                                Usuń kod
                            </button>
                        </div>
                    </div>

                    {{-- Formularz adresu --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="city" class="block font-semibold mb-1">Miasto:</label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                required
                                placeholder="Np. Warszawa"
                                class="input input-bordered w-full"
                                value="{{ old('city', $cart->city ?? '') }}"
                            >
                        </div>
                        <div>
                            <label for="postal_code" class="block font-semibold mb-1">Kod pocztowy:</label>
                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                required
                                placeholder="12-345"
                                pattern="\d{2}-\d{3}"
                                title="Kod pocztowy musi mieć format: 12-345"
                                class="input input-bordered w-full"
                                value="{{ old('postal_code', $cart->postal_code ?? '') }}"
                            >
                        </div>
                        <div>
                            <label for="street" class="block font-semibold mb-1">Ulica:</label>
                            <input
                                type="text"
                                id="street"
                                name="street"
                                required
                                placeholder="Np. Marszałkowska"
                                class="input input-bordered w-full"
                                value="{{ old('street', $cart->street ?? '') }}"
                            >
                        </div>
                        <div>
                            <label for="apartment_number" class="block font-semibold mb-1">Nr mieszkania / domu:</label>
                            <input
                                type="text"
                                id="apartment_number"
                                name="apartment_number"
                                required
                                placeholder="Np. 12A"
                                class="input input-bordered w-full"
                                value="{{ old('apartment_number', $cart->apartment_number ?? '') }}"
                            >
                        </div>
                </div>
                </form>

                <!-- Dostępne kody -->
                <div class="text-gray-800">
                    <p class="font-semibold mb-2">🎁 Twoje dostępne kody rabatowe:</p>
                    @if ($userDiscounts->isEmpty())
                        <p class="italic text-gray-500">Brak dostępnych kodów rabatowych.</p>
                    @else
                        <ul class="list-disc list-inside">
                            @foreach ($userDiscounts as $discount)
                                <li>
                                    <strong>{{ $discount->code }}</strong> –
                                    {{ $discount->type === 'percentage' ? "-{$discount->value}%" : "-{$discount->value} zł" }}
                                    @if ($discount->expires_at)
                                        (ważny do {{ $discount->expires_at->format('Y-m-d') }})
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Podsumowanie -->
                <div class="mt-6 p-4 border rounded bg-gray-50 text-gray-900">
                    <p><strong>Kwota przed rabatami:</strong> {{ number_format($totalBeforeDiscounts, 2) }} zł</p>
                    <p><strong>Darmowe produkty (4+1):</strong> -{{ number_format($freeAmount, 2) }} zł</p>
                    <p><strong>Rabat procentowy:</strong> -{{ number_format($bulkDiscountAmount, 2) }} zł ({{ $bulkDiscountPercent }}%)</p>
                    <hr class="my-3 border-gray-300">
                    <p class="font-bold text-lg">Kwota po rabatach: <span x-text="formattedTotal()"></span></p>

                    @if(!empty($promotionsApplied))
                        <div class="mt-3 text-green-700">
                            <p class="font-semibold">Zastosowane promocje:</p>
                            <ul class="list-disc list-inside">
                                @foreach($promotionsApplied as $promo)
                                    <li>{{ $promo }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <button
                    type="submit"
                    form="orderForm"
                    class="mt-4 w-full bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition"
                >
                    ✅ Przejdź do płatności
                </button>

                <form method="POST" action="{{ route('client.cart.clear') }}" class="mt-4">
                    @csrf @method('DELETE')
                    <button
                        onclick="return confirm('Czy na pewno opróżnić koszyk?')"
                        class="w-full bg-red-500 text-white px-4 py-3 rounded hover:bg-red-600 transition"
                    >
                        🗑️ Opróżnij koszyk
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function cartData() {
    return {
        total: 0,
        discountCode: '',
        discountValue: 0,
        discountType: null,
        discountAmount: 0,
        availableDiscounts: @json($userDiscounts->map(fn($d) => [
            'code' => strtolower($d->code),
            'value' => $d->value,
            'type' => $d->type
        ])),
        calculateTotal() {
            let items = @json($cart->items);
            let days = {{ $days }};
            let total = 0;
            let grouped = {};

            for (let item of items) {
                let price = item.unit_price * item.quantity * days;
                total += price;
                let pid = item.product_id;
                grouped[pid] = (grouped[pid] || 0) + item.quantity;
            }

            let free = 0;
            for (let [pid, qty] of Object.entries(grouped)) {
                if (qty >= 5) {
                    let product = items.find(i => i.product_id == pid)?.product;
                    if (product) {
                        free += product.price * days;
                    }
                }
            }

            total -= free;

            if (total >= 3000) total *= 0.85;
            else if (total >= 2000) total *= 0.90;

            let discount = 0;
            if (this.discountValue > 0 && this.discountType) {
                if (this.discountType === 'percentage') {
                    discount = total * (this.discountValue / 100);
                    total = total - discount;
                } else if (this.discountType === 'fixed') {
                    discount = this.discountValue;
                    total = total - discount;
                }
            }

            this.discountAmount = discount.toFixed(2);
            this.total = total.toFixed(2);
        },
        formattedTotal() {
            this.calculateTotal();
            return this.total + ' zł';
        },
        applyDiscount() {
            let code = this.discountCode.trim().toLowerCase();
            let discount = this.availableDiscounts.find(d => d.code === code);
            if (discount) {
                this.discountValue = discount.value;
                this.discountType = discount.type;

                const label = discount.type === 'percentage'
                    ? `-${discount.value.toFixed(2)}%`
                    : `-${discount.value.toFixed(2)} zł`;

                alert(`Kod rabatowy aktywowany! Rabat: ${label}`);
                this.calculateTotal();
            } else {
                alert("Nie znaleziono takiego kodu rabatowego lub jest nieaktywny.");
                this.discountCode = '';
                this.discountValue = 0;
                this.discountType = null;
                this.discountAmount = 0;
                this.calculateTotal();
            }
        },
        removeDiscount() {
            this.discountCode = '';
            this.discountValue = 0;
            this.discountType = null;
            this.discountAmount = 0;
            alert("Kod rabatowy usunięty.");
            this.calculateTotal();
        }
    }
}
</script>

@endsection
