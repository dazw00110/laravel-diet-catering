@extends('layouts.client')

@section('title', 'Twój koszyk')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-8" x-data="cartData()">
        <!-- Hero Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-800 mb-4">
                🛒 Twój koszyk
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Zarządzaj swoimi produktami cateringowymi i sfinalizuj zamówienie
            </p>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-8 max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-800 px-6 py-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <span class="text-green-600 mr-3">✅</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-8 max-w-4xl mx-auto bg-red-100 border border-red-400 text-red-800 px-6 py-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <span class="text-red-600 mr-3">❌</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($cart->items->isEmpty())
            <!-- Empty Cart -->
            <div class="text-center py-20">
                <div class="mb-8">
                    <div class="text-8xl mb-6">🍽️</div>
                    <h2 class="text-3xl font-bold text-gray-700 mb-4">Koszyk jest pusty</h2>
                    <p class="text-gray-500 text-lg max-w-md mx-auto">Dodaj produkty cateringowe, aby kontynuować zamówienie</p>
                </div>
                <a href="{{ route('client.products.index') }}"
                   class="inline-block px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    🍰 Zobacz naszą pełną ofertę
                </a>
            </div>
        @else
            @php
                $days = max($cart->start_date->diffInDays($cart->end_date) + 1, 7);
                $maxCateringDays = 60;
                $maxEndDate = $cart->start_date->copy()->addDays($maxCateringDays - 1);
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
                    $promotionsApplied[] = "Rabat {$bulkDiscountPercent}% od kwoty po 4+1 powyżej " . ($bulkDiscountPercent == 15 ? "3000" : "2000") . " zł";
                }

                $totalAfterBulk = $totalAfterFree - $bulkDiscountAmount;
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Products Section -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Date Selection -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                            📅 Czas trwania cateringu
                        </h3>
                        <p class="text-gray-600 mb-4">Wybierz daty rozpoczęcia i zakończenia cateringu</p>
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg flex items-center gap-2">
                            <span>ℹ️</span>
                            Maksymalny czas trwania cateringu to <strong>60 dni</strong>.
                        </div>

                        <!-- Date Validation -->
                        @if ($days > $maxCateringDays)
                            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg font-semibold flex items-center gap-2">
                                <span>❗</span>
                                Maksymalny czas trwania cateringu to 60 dni. Proszę wybrać krótszy okres.
                            </div>
                        @endif
                        <form method="POST" action="{{ route('client.cart.updateDates') }}"
                              class="grid grid-cols-1 md:grid-cols-2 gap-6"
                              x-data x-on:change.debounce.500ms="$el.submit()">
                            @csrf @method('PATCH')
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Data rozpoczęcia:
                                </label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ $cart->start_date->format('Y-m-d') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    max="{{ now()->addYear()->format('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                                    x-data
                                    x-on:change="
                                        let start = $event.target.value;
                                        let endInput = document.getElementById('end_date');
                                        let minEnd = new Date(start);
                                        minEnd.setDate(minEnd.getDate() + 7);
                                        let maxEnd = new Date(start);
                                        maxEnd.setDate(maxEnd.getDate() + 59);
                                        let minDateStr = minEnd.toISOString().split('T')[0];
                                        let maxDateStr = maxEnd.toISOString().split('T')[0];
                                        endInput.min = minDateStr;
                                        endInput.max = maxDateStr;
                                        if(endInput.value < minDateStr) {
                                            endInput.value = minDateStr;
                                        }
                                        if(endInput.value > maxDateStr) {
                                            endInput.value = maxDateStr;
                                        }
                                    "
                                >
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Data zakończenia:
                                </label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ $cart->end_date->format('Y-m-d') }}"
                                    min="{{ $cart->start_date->copy()->addDays(6)->format('Y-m-d') }}"
                                    max="{{ $maxEndDate->format('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                                >
                            </div>
                        </form>
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <p class="text-blue-800 font-medium">
                                📊 Czas trwania cateringu: <strong>{{ $days }} dni</strong>
                            </p>
                            @if ($days > $maxCateringDays)
                                <p class="text-red-700 font-bold mt-2">Maksymalny czas trwania cateringu to 60 dni!</p>
                            @endif
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-800 text-white p-6">
                            <h2 class="text-2xl font-bold">🍽️ Produkty w koszyku</h2>
                            <p class="text-gray-300 mt-1">{{ $cart->items->count() }} produktów w koszyku</p>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach ($cart->items->sortBy('created_at') as $item)
                                <div class="flex flex-col md:flex-row gap-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                                     x-data="{ qty: {{ $item->quantity }} }">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <img src="{{ $item->product->image_url }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-24 h-24 object-cover rounded-lg">
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $item->product->name }}</h3>
                                        <p class="text-gray-600 text-sm mt-1">{{ $item->product->description ?? 'Opis produktu' }}</p>
                                        <div class="flex gap-3 mt-2 text-sm">
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                                {{ number_format($item->unit_price, 2) }} zł/dzień
                                            </span>
                                            @if($item->product->is_vegan)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">🌱 Wegańska</span>
                                            @elseif($item->product->is_vegetarian)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">🥬 Wegetariańska</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="flex items-center gap-3">
                                            <button
                                                @click="
                                                    if(qty > 1) {
                                                        qty--;
                                                        $nextTick(() => {
                                                            $dispatch('update-qty', {id: {{ $item->id }}, qty: qty});
                                                            calculateTotal();
                                                        });
                                                    }
                                                "
                                                class="w-10 h-10 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-bold transition-colors">
                                                −
                                            </button>
                                            <span class="mx-4 font-bold text-xl min-w-[3rem] text-center" x-text="qty"></span>
                                            <button
                                                @click="
                                                    if(qty < 10) {
                                                        qty++;
                                                        $nextTick(() => {
                                                            $dispatch('update-qty', {id: {{ $item->id }}, qty: qty});
                                                            calculateTotal();
                                                        });
                                                    }
                                                "
                                                class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-full flex items-center justify-center text-blue-700 font-bold transition-colors">
                                                +
                                            </button>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-bold text-lg text-green-600">
                                                <span x-text="(qty * {{ $item->unit_price }} * {{ $days }}).toFixed(2)"></span> zł
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $item->unit_price }} × <span x-text="qty"></span> × {{ $days }} dni
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="flex flex-col justify-center">
                                        <form method="POST" action="{{ route('client.cart.remove', $item) }}" onsubmit="return confirm('Na pewno usunąć ten produkt?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 bg-red-100 hover:bg-red-200 rounded-full flex items-center justify-center text-red-600 transition-colors">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="xl:col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-6">
                        <div class="bg-green-600 text-white p-6">
                            <h3 class="text-2xl font-bold">💳 Podsumowanie</h3>
                            <p class="text-green-100 mt-1">Sprawdź szczegóły zamówienia</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Discount Code Section -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">🎁 Kod rabatowy:</label>
                                <div class="space-y-3">
                                    <input
                                        type="text"
                                        placeholder="Wpisz kod rabatowy"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        x-model="discountCode"
                                        @input="calculateTotal()"
                                    />
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            @click="applyDiscount"
                                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium text-sm"
                                        >
                                            ✅ Zastosuj
                                        </button>
                                        <button
                                            type="button"
                                            @click="removeDiscount"
                                            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium text-sm"
                                        >
                                            ❌ Usuń
                                        </button>
                                    </div>
                                </div>

                                <!-- Discount Status -->
                                <div x-show="discountValue > 0" x-transition class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center text-green-700">
                                        <span class="text-green-500 mr-2">✅</span>
                                        <span class="font-medium">Kod aktywny: </span>
                                        <span class="font-bold ml-1" x-text="discountCode ? discountCode.toUpperCase() : 'Brak'"></span>
                                    </div>
                                    <div class="text-sm text-green-600 mt-1">
                                        Rabat: <span x-text="discountType === 'percentage' ? '-' + discountValue + '%' : '-' + discountValue + ' zł'"></span>
                                    </div>
                                </div>
                                <div x-show="!discountValue || !discountCode" x-transition class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="text-gray-700 font-medium">Kod rabatowy: Brak</span>
                                </div>
                            </div>

                            <!-- Available Discounts -->
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-3">🎁 Dostępne kody:</p>
                                @if ($userDiscounts->isEmpty())
                                    <p class="text-gray-500 text-sm italic">Brak dostępnych kodów</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($userDiscounts as $discount)
                                            @if (!$discount->expires_at || $discount->expires_at->isFuture())
                                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <div class="font-bold text-blue-800 text-sm">{{ $discount->code }}</div>
                                                    <div class="text-xs text-blue-600">
                                                        {{ $discount->type === 'percentage' ? "-{$discount->value}%" : "-{$discount->value} zł" }}
                                                        @if ($discount->expires_at)
                                                            <br>Ważny do: {{ $discount->expires_at->format('d.m.Y') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Price Breakdown -->
                            <div class="border-t pt-4">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Kwota przed rabatami:</span>
                                        <span class="font-medium">{{ number_format($totalBeforeDiscounts, 2) }} zł</span>
                                    </div>
                                    @if($freeAmount > 0)
                                        <div class="flex justify-between text-green-600">
                                            <span>Darmowe produkty (4+1):</span>
                                            <span class="font-medium">-{{ number_format($freeAmount, 2) }} zł</span>
                                        </div>
                                    @endif
                                    @if($bulkDiscountPercent > 0)
                                        <div class="flex justify-between text-blue-600">
                                            <span>Rabat {{ $bulkDiscountPercent }}%:</span>
                                            <span class="font-medium">-{{ number_format($bulkDiscountAmount, 2) }} zł</span>
                                        </div>
                                    @endif
                                    <div x-show="discountCode" x-transition class="flex justify-between text-purple-600">
                                        <span>Kod rabatowy:</span>
                                        <span class="font-medium">-<span x-text="discountAmount"></span> zł</span>
                                    </div>
                                </div>
                                <div class="border-t mt-4 pt-4">
                                    <div class="flex justify-between items-center text-xl">
                                        <span class="font-bold text-gray-800">Razem do zapłaty:</span>
                                        <span class="font-bold text-green-600" x-text="formattedTotal()"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Applied Promotions -->
                            @if(!empty($promotionsApplied))
                                <div class="border-t pt-4">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">🎉 Aktywne promocje:</p>
                                    <div class="space-y-2">
                                        @foreach($promotionsApplied as $promo)
                                            <div class="p-2 bg-green-50 border border-green-200 rounded text-xs text-green-700">
                                                {{ $promo }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Address Form -->
                            <form method="POST" action="{{ route('client.orders.store') }}" id="orderForm" class="border-t pt-4">
                                @csrf
                                <input type="hidden" name="discount_code" x-bind:value="discountCode">

                                <h4 class="text-sm font-semibold mb-3 text-gray-700">📍 Adres dostawy:</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2">
                                        <input
                                            type="text"
                                            name="city"
                                            required
                                            placeholder="Miasto"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            value="{{ old('city', $cart->city ?? '') }}"
                                        >
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            name="postal_code"
                                            required
                                            placeholder="Kod pocztowy"
                                            pattern="\d{2}-\d{3}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            value="{{ old('postal_code', $cart->postal_code ?? '') }}"
                                        >
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            name="street"
                                            required
                                            placeholder="Ulica"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            value="{{ old('street', $cart->street ?? '') }}"
                                        >
                                    </div>
                                    <div class="col-span-2">
                                        <input
                                            type="text"
                                            name="apartment_number"
                                            required
                                            placeholder="Nr mieszkania/domu"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
                                            value="{{ old('apartment_number', $cart->apartment_number ?? '') }}"
                                        >
                                    </div>
                                </div>
                            </form>

                            <!-- Action Buttons -->
                            <div class="space-y-3 border-t pt-4">
                                <button
                                    type="submit"
                                    form="orderForm"
                                    class="w-full bg-green-600 text-white px-6 py-4 rounded-lg hover:bg-green-700 transition font-bold text-lg shadow-lg"
                                >
                                    🛒 Kup
                                </button>

                                <form method="POST" action="{{ route('client.cart.clear') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="w-full bg-gray-500 text-white px-4 py-3 rounded-lg hover:bg-gray-600 transition font-medium"
                                    >
                                        🗑️ Opróżnij koszyk
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- discounts and promotions section -->
            <div class="mt-8 p-4 border rounded bg-blue-50 text-blue-900">
                <h4 class="font-bold mb-2">Jak możesz uzyskać rabaty?</h4>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    <li><strong>Kod rabatowy:</strong> Możesz użyć dostępnych kodów rabatowych (sekcja powyżej).</li>
                    <li><strong>4+1 gratis:</strong> Za każde 5 sztuk tego samego produktu – 1 sztuka gratis (liczone na cały okres zamówienia).</li>
                    <li><strong>Duże zamówienie:</strong> Rabat 10% dla zamówień powyżej 2000 zł, rabat 15% powyżej 3000 zł (po uwzględnieniu promocji 4+1).</li>
                    <li><strong>Rabat za regularność:</strong> Jeśli w ciągu ostatnich 7 dni zrealizowałeś 3 zamówienia – dodatkowy rabat 5%.</li>
                    <li><strong>Rabat za niezadowolenie:</strong> Jeśli anulujesz zamówienie i wystawisz niską ocenę (średnia &lt; 2), możesz otrzymać dodatkowy kod rabatowy.</li>
                </ul>
                <div class="mt-4 text-sm text-blue-800 border-t pt-3">
                    <strong>Rabat lojalnościowy:</strong>
                    Po wydaniu 10 000 zł otrzymasz stałą zniżkę -5%, a po przekroczeniu 15 000 zł -10% na każde zamówienie.<br>
                    <span class="italic text-blue-700">Funkcjonalność nie jest jeszcze dostępna – wprowadzimy ją w przyszłości.</span>
                </div>

                <!-- How discounts are calculated -->
                <div class="mt-8 p-4 border rounded bg-blue-100 text-blue-900">
                    <h4 class="font-bold mb-2">Jak są naliczane rabaty?</h4>
                    <ol class="list-decimal list-inside space-y-1 text-sm">
                        <li><strong>Najpierw</strong> sumowana jest cena wszystkich produktów za cały okres zamówienia.</li>
                        <li><strong>Następnie</strong> odejmowane są darmowe produkty (4+1 gratis).</li>
                        <li><strong>Później</strong> liczony jest rabat 10% (jeśli po 4+1 jest &ge; 2000 zł) lub 15% (jeśli po 4+1 jest &ge; 3000 zł).</li>
                        <li><strong>Potem</strong> liczony jest rabat lojalnościowy 5% (jeśli masz 3 zamówienia w ostatnich 7 dniach).</li>
                        <li><strong>Na końcu</strong> od tej kwoty liczony jest rabat z kodu rabatowego:<br>
                            - <strong>procentowy</strong> (np. -19%)<br>
                            - <strong>kwotowy</strong> (np. -147 zł, -153 zł)<br>
                            Rabat z kodu jest zawsze liczony od kwoty po wszystkich powyższych rabatach.
                        </li>
                    </ol>
                    <div class="mt-4 text-sm text-blue-800 border-t pt-3">
                        <strong>Przykład:</strong> Jeśli masz kod rabatowy -19%, to rabat ten zostanie naliczony od kwoty po wszystkich innych promocjach (czyli po 4+1 gratis, rabacie 10%/15% i ewentualnym rabacie lojalnościowym).
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<script>
function cartData() {
    return {
        total: 0,
        discountCode: '',
        discountValue: 0,
        discountType: null,
        discountAmount: 0,
        showToast: false,
        toastMessage: '',
        availableDiscounts: @json($userDiscounts->map(fn($d) => [
            'code' => strtolower($d->code),
            'value' => $d->value,
            'type' => $d->type
        ])),
        maxCateringDays: 60,

        init() {
            this.calculateTotal();
            document.addEventListener('update-qty', async (e) => {
                await this.updateQuantity(e.detail.id, e.detail.qty);
                this.calculateTotal();
            });
        },

        calculateTotal() {
            let items = @json($cart->items);
            let days = {{ $days }};
            let total = 0;
            let grouped = {};

            // Calculate base total
            for (let item of items) {
                let price = item.unit_price * item.quantity * days;
                total += price;
                let pid = item.product_id;
                grouped[pid] = (grouped[pid] || 0) + item.quantity;
            }

            // Apply 4+1 free promotion
            let free = 0;
            for (let [pid, qty] of Object.entries(grouped)) {
                if (qty >= 5) {
                    let freeSets = Math.floor(qty / 5);
                    let product = items.find(i => i.product_id == pid)?.product;
                    if (product) {
                        free += freeSets * product.price * days;
                    }
                }
            }
            let afterFree = total - free;

            // Bulk discount od afterFree
            let bulkDiscountPercent = 0;
            if (afterFree >= 3000) {
                bulkDiscountPercent = 15;
            } else if (afterFree >= 2000) {
                bulkDiscountPercent = 10;
            }
            let bulkDiscount = afterFree * (bulkDiscountPercent / 100);
            let afterBulk = afterFree - bulkDiscount;

            // Discount code from afterBulk
            let discount = 0;
            if (this.discountValue > 0 && this.discountType) {
                if (this.discountType === 'percentage') {
                    discount = afterBulk * (this.discountValue / 100);
                } else if (this.discountType === 'fixed') {
                    discount = Math.min(this.discountValue, afterBulk);
                }
            }
            this.discountAmount = discount.toFixed(2);
            this.total = (afterBulk - discount).toFixed(2);

            // JS lock: if days > 60, show an alert and do not count further
            if (days > this.maxCateringDays) {
                this.total = '0.00';
                this.discountAmount = '0.00';
                this.showMessage('Maksymalny czas trwania cateringu to 60 dni!', 'error');
                return;
            }
        },

        formattedTotal() {
            return this.total + ' zł';
        },

        async updateQuantity(itemId, newQty) {
            if (newQty < 1) newQty = 1;
            try {
                const response = await fetch('{{ route('client.cart.update') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        item_id: itemId,
                        quantity: newQty
                    })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    this.showMessage('Wystąpił błąd podczas aktualizacji koszyka', 'error');
                }
            } catch (error) {
                this.showMessage('Wystąpił błąd podczas aktualizacji koszyka', 'error');
            }
        },

        applyDiscount() {
            let code = this.discountCode.trim().toLowerCase();
            if (!code) {
                this.showMessage("Wprowadź kod rabatowy!", "error");
                return;
            }
            let discount = this.availableDiscounts.find(d => d.code === code);
            if (discount) {
                this.discountValue = discount.value;
                this.discountType = discount.type;
                this.calculateTotal(); // <-- natychmiast przelicz rabat
                const label = discount.type === 'percentage'
                    ? `-${discount.value}%`
                    : `-${discount.value.toFixed(2)} zł`;
                this.showMessage(`✅ Kod rabatowy aktywowany! Rabat: ${label}`, "success");
            } else {
                this.showMessage("❌ Nie znaleziono takiego kodu rabatowego lub jest nieaktywny.", "error");
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
            this.showMessage("🗑️ Kod rabatowy usunięty.", "info");
            this.calculateTotal();
        },

        showMessage(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-100 border border-green-400 text-green-700'
                : type === 'error' ? 'bg-red-100 border border-red-400 text-red-700'
                : 'bg-blue-100 border border-blue-400 text-blue-700'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }
}
</script>
@endsection
