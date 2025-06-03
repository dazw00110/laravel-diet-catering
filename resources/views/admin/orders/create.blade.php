@extends('layouts.admin')

@section('title', 'Dodaj zamówienie')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg p-10 rounded-xl" x-data="orderForm()">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dodaj zamówienie</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded mb-6">
            <strong>Błąd:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klient</label>
                <select name="user_id" class="input input-bordered w-full">
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="input input-bordered w-full">
                    <option value="unordered">🛒 W koszyku</option>
                    <option value="in_progress">🔹 W trakcie</option>
                    <option value="completed">✅ Zakończono</option>
                    <option value="cancelled">❌ Anulowano</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data rozpoczęcia</label>
                <input type="date" name="start_date" x-model="startDate" class="input input-bordered w-full">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data zakończenia</label>
                <input type="date" name="end_date" x-model="endDate" class="input input-bordered w-full">
                <template x-if="!validDates && startDate && endDate">
                    <p class="text-sm text-red-600 mt-1">Data zakończenia musi być co najmniej 7 dni po rozpoczęciu, maksymalnie 30.</p>
                </template>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Produkty</h2>

            <template x-for="(item, i) in items" :key="i">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <select :name="'items[' + i + '][product_id]'" x-model="item.product_id" class="input input-bordered">
                        <option value="">-- wybierz produkt --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} – {{ number_format($product->price, 2) }} zł</option>
                        @endforeach
                    </select>

                    <input type="number" min="1" max="10" :name="'items[' + i + '][quantity]'" x-model="item.quantity"
                        class="input input-bordered" placeholder="Ilość (max 10)">

                    <button type="button"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-semibold"
                            @click="removeItem(i)">
                        ✖️ Usuń
                    </button>
                </div>
            </template>

            <button type="button"
                    class="btn btn-outline btn-accent mt-2"
                    :disabled="!canAddMore()"
                    @click="addItem()">
                ➕ Dodaj produkt
            </button>

            <template x-if="!canAddMore()">
                <p class="text-sm text-red-600 mt-2">Maksymalnie 10 produktów i 100 jednostek cateringu.</p>
            </template>
        </div>

        <div class="mt-8">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kod rabatowy (opcjonalny)</label>
            <div class="flex gap-4">
                <input type="text" x-model="discountCodeInput" placeholder="Wprowadź kod rabatowy" class="input input-bordered w-full">
                <button type="button"
                        @click="applyDiscountCode"
                        class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                    🎟️ Sprawdź kod
                </button>
            </div>

            <template x-if="discount">
                <p class="text-sm text-green-700 mt-1">
                    ✔️ Kod <strong x-text="discountCode"></strong> zastosowany
                </p>
            </template>

            <template x-if="discountChecked && !discount && discountCodeInput">
                <p class="text-sm text-red-600 mt-1">Nieprawidłowy kod rabatowy</p>
            </template>

            <input type="hidden" name="discount_code" :value="discountCode">
        </div>

        <div class="mt-10 bg-gray-100 p-4 rounded-md">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-lg text-gray-700">Podsumowanie:</h3>
                <div class="relative group">
                    <span class="cursor-pointer text-blue-600 font-bold text-lg">ℹ️</span>
                    <div class="absolute z-10 w-72 p-3 mt-2 text-sm text-left text-gray-800 bg-white border rounded shadow-md opacity-0 group-hover:opacity-100 transition duration-200">
                        <p><strong>Rodzaje zniżek:</strong></p>
                        <ul class="list-disc list-inside">
                            <li><strong>Kod rabatowy:</strong> -X% lub -X zł</li>
                            <li><strong>4+1 gratis:</strong> przy ≥ 5 sztuk produktu</li>
                            <li><strong>-15%:</strong> za koszyk powyżej 3000 zł</li>
                            <li><strong>-10%:</strong> za koszyk powyżej 2000 zł</li>
                            <li><strong>-5%:</strong> za 3+ zamówienia w ostatnim tygodniu</li>
                        </ul>
                    </div>
                </div>
            </div>

            <ul class="text-sm text-gray-700 list-disc pl-6">
                <template x-for="summary in summaryItems" :key="summary.text">
                    <li x-show="summary.valid" x-text="summary.text"></li>
                </template>
            </ul>

            <template x-if="freeItemBonus > 0">
                <p class="mt-2 text-green-600 text-sm">🎁 Promocja 4+1: odjęto <strong x-text="freeItemBonus.toFixed(2) + ' zł'"></strong></p>
            </template>

            <template x-if="totalBeforeFinal >= 1000">
                <p class="mt-1 text-green-600 text-sm">💸 Zniżka -10% za duże zamówienie</p>
            </template>

            <template x-if="recentOrders >= 3">
                <p class="mt-1 text-green-600 text-sm">🔁 Lojalność: -5% za 3+ zamówienia w tygodniu</p>
            </template>

            <p class="text-xl font-bold mt-4 text-right">
                Suma: <span x-text="validDates && hasValidItems ? totalPrice + ' zł' : '0 zł'"></span>
            </p>
        </div>

        <button type="submit"
                class="btn bg-green-600 hover:bg-green-700 text-white text-lg w-full mt-6 rounded-md font-semibold py-3">
            ✔️ Zapisz zamówienie
        </button>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: [{ product_id: '', quantity: 1 }],
        startDate: '',
        endDate: '',
        products: @json($products),
        discounts: @json(\App\Models\Discount::all()),
        discountCodeInput: '',
        discountCode: '',
        discount: null,
        discountChecked: false,
        freeItemBonus: 0,
        totalBeforeFinal: 0,
        recentOrders: {{ $recentOrders ?? 0 }},

        addItem() {
            if (this.canAddMore()) {
                this.items.push({ product_id: '', quantity: 1 });
            }
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        applyDiscountCode() {
            this.discountChecked = true;
            const code = this.discountCodeInput.trim().toLowerCase();
            this.discount = this.discounts.find(d => d.code.toLowerCase() === code) || null;
            this.discountCode = code;
        },
        get numberOfDays() {
            if (!this.validDates) return 0;
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            return (end - start) / (1000 * 60 * 60 * 24) + 1;
        },
        get validDates() {
            if (!this.startDate || !this.endDate) return false;
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            const diff = (end - start) / (1000 * 60 * 60 * 24);
            return diff >= 6 && diff <= 30;
        },
        get summaryItems() {
            if (!this.validDates) return [];
            return this.items.map(item => {
                const product = this.products.find(p => p.id == item.product_id);
                const valid = product && item.quantity > 0 && item.quantity <= 10;
                const price = product ? product.price : 0;
                const subtotal = price * item.quantity * this.numberOfDays;
                return {
                    valid,
                    text: valid ? `${product.name} × ${item.quantity} × ${this.numberOfDays} dni = ${subtotal.toFixed(2)} zł` : ''
                };
            });
        },
        get hasValidItems() {
            return this.summaryItems.filter(item => item.valid).length > 0;
        },
        get totalQuantity() {
            return this.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
        },
        canAddMore() {
            return this.items.length < 10 && this.totalQuantity < 100;
        },
        get totalPrice() {
            let total = this.summaryItems
                .filter(item => item.valid)
                .reduce((acc, item) => {
                    const match = item.text.match(/= ([\d.]+) zł$/);
                    return match ? acc + parseFloat(match[1]) : acc;
                }, 0);

            this.totalBeforeFinal = total;

            // 4+1 gratis
            const productsCount = {};
            this.freeItemBonus = 0;
            for (const item of this.items) {
                if (!item.product_id || !item.quantity) continue;
                productsCount[item.product_id] = (productsCount[item.product_id] || 0) + Number(item.quantity);
            }
            for (const [pid, qty] of Object.entries(productsCount)) {
                if (qty >= 5) {
                    const product = this.products.find(p => p.id == pid);
                    if (product) {
                        this.freeItemBonus += product.price * this.numberOfDays;
                    }
                }
            }
            total -= this.freeItemBonus;

            if (total >= 1000) total *= 0.90;
            if (this.recentOrders >= 3) total *= 0.95;

            if (this.discount) {
                if (this.discount.type === 'percentage') {
                    total *= (1 - this.discount.value / 100);
                } else if (this.discount.type === 'fixed') {
                    total -= this.discount.value;
                }
            }

            return total.toFixed(2);
        }
    }
}
</script>
@endsection
