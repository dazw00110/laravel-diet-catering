@extends('layouts.main')

@section('title', 'Strona główna')

@section('content')
<div>
    <!-- 🔥 HERO -->
    <section class="relative h-[500px] bg-cover bg-center text-white"
             style="background-image: url('https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white text-center">FitBox Catering</h1>
        </div>
    </section>

    <!-- ✨ KARUZELA -->
    <section class="py-16 bg-gray-50"
        x-data="{
            start: 0,
            max: {{ count($products) }},
            visible: 4,
            loop: null,
            showToast: false,
            message: '',
            next() {
                this.start = (this.start + 1) % this.max;
                if (this.start > this.max - this.visible) this.start = 0;
            },
            prev() {
                this.start = (this.start - 1 + this.max) % this.max;
                if (this.start > this.max - this.visible) this.start = this.max - this.visible;
            }
        }"
        x-init="loop = setInterval(() => next(), 5000)">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-extrabold text-gray-800 mb-4">🍰 Odkryj nasz catering</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    Od ponad 10 lat tworzymy jedną z największych firm cateringowych w Polsce.
                    Codziennie dostarczamy tysiące pudełek z dietą, na której możesz polegać.
                    Sprawdź, co polecają nasi klienci!
                </p>
            </div>

            <div class="relative mx-auto max-w-[1040px]">
                <button @click="prev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full shadow px-3 py-1 text-lg hover:bg-gray-100">
                    ←
                </button>
                <button @click="next"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white border rounded-full shadow px-3 py-1 text-lg hover:bg-gray-100">
                    →
                </button>

                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-300 ease-in-out"
                         :style="'transform: translateX(-' + (start * 260) + 'px)'"
                         style="width: {{ count($products) * 260 }}px">
                        @foreach ($products as $product)
                            <div class="w-[260px] flex-shrink-0 p-2">
                                <div class="bg-white rounded shadow-md p-4 flex flex-col justify-between h-full"
                                     x-data="{
                                        async addToCart() {
                                            const response = await fetch('{{ route('client.cart.add', $product) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                },
                                                body: JSON.stringify({ quantity: 1 })
                                            });
                                            if (response.ok) {
                                                this.$root.showToast = true;
                                                this.$root.message = 'Dodano do koszyka!';
                                                setTimeout(() => this.$root.showToast = false, 3000);
                                            }
                                        }
                                     }">
                                    <img src="{{ $product->image_url ?: asset('storage/default-product.png') }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-[180px] object-cover rounded mb-3" />

                                    <div class="h-[250px] flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-base font-semibold">{{ $product->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $product->description }}</p>
                                        </div>

                                        <div>
                                            <p class="text-green-600 font-bold text-sm">
                                                {{ number_format($product->getCurrentPrice(), 2) }} zł
                                                <span class="text-gray-600 font-normal">/dzień</span>
                                            </p>

                                            <div class="flex gap-2 mt-2 text-sm text-gray-700">
                                                <div>{{ $product->is_vegan ? '✅ Wegan' : '❌ Wegan' }}</div>
                                                <div>{{ $product->is_vegetarian ? '✅ Wegetariańska' : '❌ Wegetariańska' }}</div>
                                            </div>

                                            <p class="mt-2 text-sm text-gray-700">
                                                Ocena: {{ number_format($product->reviews_avg_rating, 1) }}/5
                                            </p>

                                            @php $review = $product->reviews->first(); @endphp
                                            @if ($review)
                                                <div class="text-xs text-gray-500 italic mt-1">
                                                    "{{ $review->comment }}" – {{ $review->user->first_name }} {{ $review->user->last_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('guest.offers') }}"
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 text-base font-semibold">
                    Zobacz naszą pełną ofertę
                </a>
            </div>
        </div>

        <!-- ✅ TOAST w prawym dolnym rogu -->
        <div x-show="showToast" x-transition
             class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded shadow-lg z-50 flex items-center space-x-4"
             x-cloak>
            <span x-text="message"></span>
            <a href="{{ route('client.cart.index') }}" class="underline text-sm font-semibold">Przejdź do koszyka</a>
        </div>
    </section>

    <!-- 📞 KONTAKT -->
    <section class="bg-white py-16 mt-16 border-t">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-2xl font-bold mb-4">Masz wątpliwości? Chcesz o coś zapytać?</h2>
            <p class="text-gray-600 mb-6">Zajrzyj na stronę kontaktową lub napisz do nas bezpośrednio – chętnie pomożemy!</p>
            <a href="{{ route('client.contact') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                Przejdź do kontaktu
            </a>
        </div>
    </section>
</div>
@endsection
