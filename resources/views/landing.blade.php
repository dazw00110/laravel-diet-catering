@extends('layouts.main')

@section('title', 'Strona główna')

@section('content')
<div>
    <!-- 🔥 HERO -->
    <section class="relative h-[420px] md:h-[500px] bg-cover bg-center text-white rounded-b-3xl shadow-lg overflow-hidden"
             style="background-image: url('https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/30 flex flex-col items-center justify-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white text-center drop-shadow-lg mb-4">FitBox Catering</h1>
            <p class="text-lg md:text-2xl text-gray-100 text-center max-w-2xl mx-auto drop-shadow">
                Najlepszy catering dietetyczny w Twoim mieście. Zadbaj o zdrowie i wygodę z naszymi zestawami!
            </p>
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

            <div class="relative mx-auto max-w-[1040px]"> {{-- 4 * 260px --}}
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
                                <div class="bg-white rounded-2xl shadow-lg p-4 flex flex-col justify-between h-full border border-gray-100 hover:shadow-xl transition-shadow duration-200">
                                    <div class="relative mb-3">
                                        <img
                                            src="{{ $product->image_url }}"
                                            alt="{{ $product->name }}"
                                            class="product-image w-full h-[160px] object-contain rounded-lg bg-gray-50"
                                        />

                                        @if($product->is_vegan)
                                            <span class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full shadow">🌱 Wegańska</span>
                                        @endif
                                        @if($product->is_vegetarian)
                                            <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full shadow">🥬 Wegetariańska</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col justify-between flex-1">
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($product->description, 60) }}</p>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-green-600 font-bold text-base">
                                                    {{ number_format($product->getCurrentPrice(), 2) }} zł
                                                </span>
                                                <span class="text-gray-600 font-normal text-xs">/dzień</span>
                                            </div>
                                            <div class="flex gap-2 mt-1 text-xs text-gray-700">
                                                @if($product->is_vegan)
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">🌱 Wegańska</span>
                                                @endif
                                                @if($product->is_vegetarian)
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">🥬 Wegetariańska</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1 mt-2">
                                                <span class="text-yellow-500 text-base flex items-center h-6">
                                                    @php
                                                        $avg = $product->reviews->count() > 0
                                                            ? $product->reviews->avg('rating')
                                                            : null;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= round($avg ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                                    @endfor
                                                </span>
                                                <span class="text-sm text-gray-700 ml-1">
                                                    {{ $product->reviews->count() > 0 ? number_format($product->reviews->avg('rating'), 1) : '0.0' }}/5
                                                </span>
                                            </div>
                                            @php $review = $product->reviews->first(); @endphp
                                            @if ($review)
                                                <div class="text-xs text-gray-500 italic mt-1">
                                                    "{{ Str::limit($review->comment, 60) }}" – {{ $review->user->first_name }} {{ $review->user->last_name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        @if(auth()->check())
                                            <form method="POST" action="{{ route('client.cart.add', $product) }}">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit"
                                                    class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm font-semibold shadow transition">
                                                    ➕ Dodaj do koszyka
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}"
                                               class="w-full block text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm font-semibold shadow transition">
                                                ➕ Dodaj do koszyka
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('guest.offers') }}"
                   class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-base font-semibold shadow">
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
