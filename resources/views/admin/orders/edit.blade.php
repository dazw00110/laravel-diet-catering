@extends('layouts.admin')

@section('title', 'Edytuj zamówienie')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg p-10 rounded-xl" x-data="orderForm()">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Edytuj zamówienie</h1>

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

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klient</label>
                <select name="user_id" class="input input-bordered w-full" disabled>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ $client->id == $order->user_id ? 'selected' : '' }}>
                            {{ $client->first_name }} {{ $client->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" x-model="status" class="input input-bordered w-full">
                    <option value="unordered">🛒 W koszyku</option>
                    <option value="in_progress">🟡 W trakcie</option>
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Kod rabatowy użyty w zamówieniu</label>
            <input type="text" class="input input-bordered w-full" value="{{ $order->discount_code }}" disabled>
        </div>
        <div class="mt-4 text-sm text-gray-600">
    <p><strong>Dostępne rabaty:</strong></p>
    <ul class="list-disc list-inside">
        <li><strong>Kod rabatowy:</strong> -X% lub -X zł (jeden raz)</li>
        <li><strong>4+1 gratis:</strong> przy ≥ 5 sztuk produktu</li>
        <li><strong>-10%:</strong> za zamówienie powyżej 2000 zł</li>
        <li><strong>-15%:</strong> za zamówienie powyżej 3000 zł</li>
        <li><strong>-5%:</strong> za 3+ zamówienia w ostatnim tygodniu</li>
    </ul>
</div>


        <div class="mt-10 bg-gray-100 p-4 rounded-md">
    <h3 class="font-semibold text-lg text-gray-700 mb-2">Podsumowanie:</h3>

    <ul class="text-sm text-gray-700 list-disc pl-6">
        <template x-for="summary in summaryItems" :key="summary.text">
            <li x-show="summary.valid" x-text="summary.text"></li>
        </template>
    </ul>

    <template x-if="freeItemBonus > 0">
        <p class="mt-2 text-green-600 text-sm">
            🎁 Promocja 4+1: odjęto
            <strong x-text="freeItemBonus.toFixed(2) + ' zł'"></strong>
        </p>
    </template>

    <template x-if="totalBeforeFinal >= 3000">
        <p class="mt-1 text-green-600 text-sm">🔥 Zniżka -15% za zamówienie powyżej 3000 zł</p>
    </template>

    <template x-if="totalBeforeFinal >= 2000 && totalBeforeFinal < 3000">
        <p class="mt-1 text-green-600 text-sm">💸 Zniżka -10% za zamówienie powyżej 2000 zł</p>
    </template>

    <template x-if="recentOrders >= 3">
        <p class="mt-1 text-green-600 text-sm">🔁 Lojalność: -5% za 3+ zamówienia w tygodniu</p>
    </template>

        <template x-if="discount">
            <p class="mt-1 text-green-600 text-sm">
                🎟️ Zastosowano kod rabatowy:
                <strong x-text="discountCode"></strong>:
                <span x-text="discount.type === 'percentage' ? `-${discount.value}%` : `-${discount.value} zł`"></span>
            </p>
        </template>



    <p class="text-xl font-bold mt-4 text-right">
        <span x-text="hasValidItems ? totalPrice + ' zł' : '0 zł'"></span>
    </p>
</div>

        <button type="submit"
                class="btn bg-green-600 hover:bg-green-700 text-white text-lg w-full mt-6 rounded-md font-semibold py-3">
            ✔️ Zapisz zmiany
        </button>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: @json($order->items->map(fn($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity])),
        startDate: '{{ $order->start_date }}',
        endDate: '{{ $order->end_date }}',
        status: '{{ $order->status }}',
        products: @json($products),
        recentOrders: {{ $recentOrders ?? 0 }},

        discountCodeInput: '{{ $order->discount_code }}',
        discount: @json($discount),
        discountCode: '{{ $order->discount_code }}',
        discountChecked: true,

        freeItemBonus: 0,
        totalBeforeFinal: 0,

        addItem() {
            if (this.canAddMore()) this.items.push({ product_id: '', quantity: 1 });
        },
        removeItem(i) {
            this.items.splice(i, 1);
        },
        get numberOfDays() {
            if (!this.startDate || !this.endDate) return 0;
            return (new Date(this.endDate) - new Date(this.startDate)) / 86400000 + 1;
        },
        get summaryItems() {
            return this.items.map(item => {
                const p = this.products.find(pr => pr.id == item.product_id);
                const valid = p && item.quantity > 0 && item.quantity <= 10;
                const subtotal = p ? p.price * item.quantity * this.numberOfDays : 0;
                return {
                    valid,
                    text: valid ? `${p.name} × ${item.quantity} × ${this.numberOfDays} dni = ${subtotal.toFixed(2)} zł` : ''
                };
            });
        },
        get hasValidItems() {
            return this.summaryItems.some(i => i.valid);
        },
        get totalQuantity() {
            return this.items.reduce((sum, i) => sum + Number(i.quantity || 0), 0);
        },
        canAddMore() {
            return this.items.length < 10 && this.totalQuantity < 100;
        },
        get totalPrice() {
            let total = this.summaryItems
                .filter(i => i.valid)
                .reduce((sum, i) => {
                    const match = i.text.match(/= ([\d.]+) zł$/);
                    return match ? sum + parseFloat(match[1]) : sum;
                }, 0);

            this.totalBeforeFinal = total;

            // 4+1 gratis
            this.freeItemBonus = 0;
            const countByProduct = {};
            for (const i of this.items) {
                if (!i.product_id || !i.quantity) continue;
                countByProduct[i.product_id] = (countByProduct[i.product_id] || 0) + Number(i.quantity);
            }
            for (const [id, qty] of Object.entries(countByProduct)) {
                if (qty >= 5) {
                    const p = this.products.find(pr => pr.id == id);
                    if (p) this.freeItemBonus += p.price * this.numberOfDays;
                }
            }
            total -= this.freeItemBonus;

            // rabat -10% / -15%
            if (this.totalBeforeFinal >= 3000) {
                total *= 0.85;
            } else if (this.totalBeforeFinal >= 2000) {
                total *= 0.90;
            }

            // rabat -5% lojalność
            if (this.recentOrders >= 3) {
                total *= 0.95;
            }

            // kod rabatowy
            if (this.discount) {
                if (this.discount.type === 'percentage') {
                    total *= 1 - this.discount.value / 100;
                } else if (this.discount.type === 'fixed') {
                    total = Math.max(0, total - this.discount.value);
                }
            }

            return total.toFixed(2);
        }
    }
}
</script>

@endsection
