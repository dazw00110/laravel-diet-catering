@extends('layouts.admin')

@section('title', 'Edytuj zamówienie')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg p-10 rounded-xl" x-data="orderForm()">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">✏️ Edytuj zamówienie #{{ $order->id }}</h1>

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

        <input type="hidden" name="user_id" value="{{ $order->user_id }}">
        <input type="hidden" name="discount_code" :value="discountCode">
        <input type="hidden" name="status" value="{{ $order->status }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klient</label>
                <input class="input input-bordered w-full bg-gray-100" disabled value="{{ $order->user->first_name }} {{ $order->user->last_name }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                @php
                    $statusLabels = [
                        'unordered' => '🛒 W koszyku',
                        'in_progress' => '🟡 W trakcie',
                        'completed' => '✅ Zakończono',
                        'cancelled' => '❌ Anulowano',
                    ];
                @endphp
                <select name="status" class="input input-bordered w-full">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                    @endforeach
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


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                <input type="text" name="city" class="input input-bordered w-full" value="{{ $order->city }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kod pocztowy</label>
                <input type="text" name="postal_code" class="input input-bordered w-full" value="{{ $order->postal_code }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ulica</label>
                <input type="text" name="street" class="input input-bordered w-full" value="{{ $order->street }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nr mieszkania</label>
                <input type="text" name="apartment_number" class="input input-bordered w-full" value="{{ $order->apartment_number }}">
            </div>
        </div>

        <div>
            <h2 class="text-xl font-semibold mt-6 mb-2">Produkty</h2>
            <template x-for="(item, i) in items" :key="i">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                    <select :name="'items[' + i + '][product_id]'" x-model="item.product_id" class="input input-bordered">
                        <option value="">-- wybierz produkt --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} – {{ number_format($product->price, 2) }} zł</option>
                        @endforeach
                    </select>
                    <input type="number" min="1" max="10" :name="'items[' + i + '][quantity]'" x-model="item.quantity" class="input input-bordered" placeholder="Ilość">
                    <button type="button" @click="removeItem(i)" class="bg-red-500 text-white px-3 py-1 rounded">Usuń</button>
                </div>
            </template>
            <button type="button" class="btn btn-outline btn-accent mt-2" @click="addItem()" :disabled="!canAddMore()">➕ Dodaj produkt</button>
        </div>

        <div class="bg-gray-100 p-4 mt-6 rounded">
            <h2 class="font-semibold text-lg mb-2">Podsumowanie</h2>
            <ul class="list-disc pl-5 text-sm text-gray-700">
                <template x-for="summary in summaryItems" :key="summary.text">
                    <li x-show="summary.valid" x-text="summary.text"></li>
                </template>
                <template x-if="freeItemBonus > 0">
                    <li class="text-green-700">🎁 4+1 gratis: oszczędzasz <strong x-text="freeItemBonus.toFixed(2) + ' zł'"></strong></li>
                </template>
                <template x-if="totalBeforeFinal >= 3000">
                    <li class="text-green-700">💰 Rabat 15% za zamówienie powyżej 3000 zł</li>
                </template>
                <template x-if="totalBeforeFinal >= 2000 && totalBeforeFinal < 3000">
                    <li class="text-green-700">💰 Rabat 10% za zamówienie powyżej 2000 zł</li>
                </template>
                @if ($recentOrders >= 3)
                    <li class="text-green-700">🔁 Lojalność: -5% za 3+ zamówienia w tygodniu</li>
                @endif
            </ul>
            <p class="mt-2 font-bold text-right">Suma: <span x-text="totalPrice + ' zł'"></span></p>
        </div>

        <button type="submit" class="btn bg-green-600 text-white w-full text-lg py-4 mt-6 font-bold">
            💾 Zapisz zmiany
        </button>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: @json($order->items->map(fn($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity])),
        products: @json($products),
        startDate: '{{ $order->start_date->format("Y-m-d") }}',
        endDate: '{{ $order->end_date->format("Y-m-d") }}',
        discountCode: '{{ $order->discount_code }}',
        freeItemBonus: 0,
        totalBeforeFinal: 0,

        addItem() { if (this.canAddMore()) this.items.push({ product_id: '', quantity: 1 }); },
        removeItem(i) { this.items.splice(i, 1); },
        canAddMore() {
            return this.items.length < 10 && this.totalQuantity < 100;
        },
        get totalQuantity() {
            return this.items.reduce((sum, i) => sum + Number(i.quantity || 0), 0);
        },
        numberOfDays() {
            if (!this.startDate || !this.endDate) return 0;
            const d1 = new Date(this.startDate);
            const d2 = new Date(this.endDate);
            return (d2 - d1) / 86400000 + 1;
        },
        get summaryItems() {
            return this.items.map(item => {
                const p = this.products.find(prod => prod.id == item.product_id);
                const valid = p && item.quantity > 0 && item.quantity <= 10;
                const subtotal = valid ? p.price * item.quantity * this.numberOfDays() : 0;
                return {
                    valid,
                    text: valid ? `${p.name} x ${item.quantity} x ${this.numberOfDays()} dni = ${subtotal.toFixed(2)} zł` : ''
                };
            });
        },
        get totalPrice() {
            let sum = this.summaryItems.filter(i => i.valid).reduce((acc, i) => {
                const match = i.text.match(/= ([\d.]+) zł/);
                return match ? acc + parseFloat(match[1]) : acc;
            }, 0);

            this.totalBeforeFinal = sum;
            this.freeItemBonus = 0;

            const productCounts = {};
            for (const item of this.items) {
                if (!item.product_id || !item.quantity) continue;
                productCounts[item.product_id] = (productCounts[item.product_id] || 0) + Number(item.quantity);
            }

            for (const [pid, qty] of Object.entries(productCounts)) {
                if (qty >= 5) {
                    const p = this.products.find(prod => prod.id == pid);
                    if (p) this.freeItemBonus += p.price * this.numberOfDays();
                }
            }

            sum -= this.freeItemBonus;
            if (sum >= 3000) sum *= 0.85;
            else if (sum >= 2000) sum *= 0.90;
            if ({{ $recentOrders ?? 0 }} >= 3) sum *= 0.95;

            return sum.toFixed(2);
        },
    }
}
</script>
@endsection
