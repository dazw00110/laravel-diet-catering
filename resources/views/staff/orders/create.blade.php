@extends('layouts.staff')

@section('title', 'Złóż zamówienie')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 shadow rounded" x-data="orderForm()">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">📝 Złóż zamówienie</h1>

<form method="POST" action="{{ route('staff.orders.store') }}">
        @csrf

        <input type="hidden" name="status" value="in_progress">

        <div>
            <label class="text-sm font-semibold">Użytkownik</label>
            <select name="user_id" class="input input-bordered w-full" x-model="selectedUser" @change="updateUserDiscounts">
                <option value="">-- wybierz użytkownika --</option>
                @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->first_name }} {{ $staff->last_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold">Data rozpoczęcia</label>
                <input type="date" name="start_date" x-model="startDate" :min="(new Date()).toISOString().split('T')[0]" class="input input-bordered w-full">
            </div>
            <div>
                <label class="text-sm font-semibold">Data zakończenia</label>
                <input type="date" name="end_date" x-model="endDate" class="input input-bordered w-full">
                <template x-if="startDate && endDate && !validDates">
                    <p class="text-red-600 text-sm mt-1">Data zakończenia musi być co najmniej 7 dni późniejsza niż rozpoczęcia i maksymalnie 30 dni.</p>
                </template>
            </div>

            <div>
                <label class="text-sm font-semibold">Miasto</label>
                <input type="text" name="city" class="input input-bordered w-full" required>
            </div>
            <div>
                <label class="text-sm font-semibold">Kod pocztowy</label>
                <input type="text" name="postal_code" class="input input-bordered w-full" required>
            </div>
            <div>
                <label class="text-sm font-semibold">Ulica</label>
                <input type="text" name="street" class="input input-bordered w-full" required>
            </div>
            <div>
                <label class="text-sm font-semibold">Nr mieszkania</label>
                <input type="text" name="apartment_number" class="input input-bordered w-full">
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold">Produkty</label>
            <template x-for="(item, i) in items" :key="i">
                <div class="grid md:grid-cols-3 gap-4 mt-2">
                    <select :name="'items['+i+'][product_id]'" x-model="item.product_id" class="input input-bordered">
                        <option value="">-- wybierz produkt --</option>
                        <template x-for="product in products" :key="product.id">
                            <option :value="product.id" x-text="product.name + ' - ' + product.price + 'zł'"></option>
                        </template>
                    </select>
                    <input type="number" min="1" max="10" :name="'items['+i+'][quantity]'" x-model="item.quantity" class="input input-bordered" placeholder="Ilość (max 10)">
                    <button type="button" @click="removeItem(i)" class="bg-red-500 hover:bg-red-600 text-white rounded px-3 py-1">Usuń</button>
                </div>
            </template>
            <button type="button" class="mt-2 btn btn-outline btn-accent" @click="addItem()" :disabled="!canAddMore()">➕ Dodaj produkt</button>
        </div>

        <div>
            <label class="text-sm font-semibold">Kod rabatowy</label>
            <div class="flex gap-4">
                <input type="text" x-model="discountCodeInput" class="input input-bordered w-full" placeholder="Masz kod?">
                <button type="button" @click="applyDiscountCode()" class="btn bg-blue-600 text-white">Zastosuj</button>
            </div>
            <template x-if="discount">
                <p class="text-green-600 text-sm mt-1">Zastosowano kod: <strong x-text="discountCode"></strong> (zniżka: <span x-text="discount.type === 'percentage' ? discount.value + '%' : discount.value + ' zł'"></span>)</p>
            </template>
            <template x-if="discountChecked && !discount">
                <p class="text-red-600 text-sm mt-1">Nieprawidłowy kod</p>
            </template>
            <template x-if="availableCodes.length">
                <div class="bg-blue-50 border border-blue-300 text-blue-800 text-sm p-3 mt-3 rounded">
                    <p class="font-semibold mb-1">🎁 Twoje dostępne kody rabatowe:</p>
                    <ul class="list-disc pl-5">
                        <template x-for="desc in availableCodes" :key="desc">
                            <li x-text="desc"></li>
                        </template>
                    </ul>
                </div>
            </template>
            <input type="hidden" name="discount_code" :value="discountCode">
        </div>

        <div class="bg-gray-100 p-4 rounded">
            <h2 class="font-semibold mb-2">Podsumowanie</h2>
            <ul class="list-disc pl-5 text-sm">
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
            </ul>
            <p class="mt-2 font-bold text-right">Suma: <span x-text="totalPrice + ' zł'"></span></p>
        </div>

        <button type="submit" class="btn bg-green-600 text-white w-full">Zamów</button>
    </form>
</div>

<script>
function orderForm() {
    return {
        startDate: '',
        endDate: '',
        selectedUser: '',
        items: [{ product_id: '', quantity: 1 }],
        products: @json($products),
        allDiscounts: @json($allDiscounts),
        discountCodeInput: '',
        discountCode: '',
        discountChecked: false,
        discount: null,
        discounts: [],
        availableCodes: [],
        freeItemBonus: 0,
        totalBeforeFinal: 0,

        updateUserDiscounts() {
            this.discounts = this.allDiscounts.filter(d => d.users.some(u => u.id == this.selectedUser));
            this.availableCodes = this.discounts.map(d =>
                `${d.code} – ${d.type === 'percentage' ? d.value + '%' : d.value + 'zł'}`
            );
        },

        numberOfDays() {
            if (!this.validDates) return 0;
            const d1 = new Date(this.startDate);
            const d2 = new Date(this.endDate);
            return Math.floor((d2 - d1) / 86400000) + 1;
        },

        get validDates() {
            if (!this.startDate || !this.endDate) return false;
            const s = new Date(this.startDate);
            const e = new Date(this.endDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const duration = (e - s) / 86400000;
            return s >= today && duration >= 6 && duration <= 30;
        },

        addItem() {
            if (this.canAddMore()) this.items.push({ product_id: '', quantity: 1 });
        },
        removeItem(i) { this.items.splice(i, 1); },
        canAddMore() {
            return this.items.length < 10 && this.totalQuantity < 100;
        },
        get totalQuantity() {
            return this.items.reduce((s, i) => s + Number(i.quantity || 0), 0);
        },

        applyDiscountCode() {
            this.discountChecked = true;
            const code = this.discountCodeInput.trim().toLowerCase();
            this.discount = this.discounts.find(d => d.code.toLowerCase() === code) || null;
            this.discountCode = code;
        },

        get summaryItems() {
            return this.items.map(item => {
                const p = this.products.find(prod => prod.id == item.product_id);
                const valid = p && item.quantity > 0 && item.quantity <= 10 && this.validDates;
                const days = this.numberOfDays();
                const subtotal = valid ? p.price * item.quantity * days : 0;
                return {
                    valid,
                    text: valid ? `${p.name} x ${item.quantity} x ${days} dni = ${subtotal.toFixed(2)} zł` : ''
                };
            });
        },

        get totalPrice() {
            let sum = this.summaryItems.filter(i => i.valid).reduce((acc, i) => {
                const match = i.text.match(/= ([\d.]+) zł/);
                return match ? acc + parseFloat(match[1]) : acc;
            }, 0);

            this.totalBeforeFinal = sum;

            const productCounts = {};
            for (const item of this.items) {
                if (!item.product_id || !item.quantity) continue;
                productCounts[item.product_id] = (productCounts[item.product_id] || 0) + Number(item.quantity);
            }

            this.freeItemBonus = 0;
            for (const [pid, qty] of Object.entries(productCounts)) {
                if (qty >= 5) {
                    const p = this.products.find(prod => prod.id == pid);
                    if (p) this.freeItemBonus += p.price * this.numberOfDays();
                }
            }

            sum -= this.freeItemBonus;

            if (sum >= 3000) sum *= 0.85;
            else if (sum >= 2000) sum *= 0.90;

            if (this.discount) {
                if (this.discount.type === 'percentage') sum *= (1 - this.discount.value / 100);
                else if (this.discount.type === 'fixed') sum -= this.discount.value;
            }

            return sum.toFixed(2);
        },
    }
}
</script>
@endsection
