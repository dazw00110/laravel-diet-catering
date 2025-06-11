@extends('layouts.client')

@section('title', 'Oferty cateringowe')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showMessage: false }">
    <h1 class="text-3xl font-bold mb-6 text-center">🥗 Oferty cateringowe</h1>

    <form method="GET" class="bg-white p-6 rounded-xl shadow mb-8 flex flex-wrap gap-6 items-end justify-center">
        <div>
            <label class="block text-sm font-medium">Szukaj</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input input-bordered w-full" placeholder="Nazwa produktu">
        </div>
        <div>
            <label class="block text-sm font-medium">Cena od</label>
            <input type="number" name="price_min" value="{{ request('price_min') }}" step="1" class="input input-bordered w-full" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium">Cena do</label>
            <input type="number" name="price_max" value="{{ request('price_max') }}" step="1" class="input input-bordered w-full" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium">Rodzaj diety</label>
            <select name="diet" class="input input-bordered w-full">
                <option value="">Wszystkie</option>
                <option value="vegan" @selected(request('diet') === 'vegan')>Wegańskie</option>
                <option value="vegetarian" @selected(request('diet') === 'vegetarian')>Wegetariańskie</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Sortuj</label>
            <select name="sort" class="input input-bordered w-full">
                <option value="">Domyślnie</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Cena rosnąco</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Cena malejąco</option>
                <option value="rating_desc" @selected(request('sort') === 'rating_desc')>Ocena malejąco</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Liczba na stronę</label>
            <select name="per_page" class="input input-bordered w-full">
                <option value="12" @selected(request('per_page') == 12)>12</option>
                <option value="24" @selected(request('per_page') == 24)>24</option>
                <option value="36" @selected(request('per_page') == 36)>36</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-white">.</label>
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow font-semibold w-full">Filtruj</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2 auto-rows-fr"
         x-data="{ showModal: false, modalProduct: null }"
         style="overflow-x: hidden;">
        @php
            $productsById = $products->keyBy('id');
        @endphp
        @forelse ($products as $product)
            <div class="w-full xl:w-[280px] bg-white shadow-lg rounded-2xl overflow-hidden flex flex-col h-full hover:scale-[1.025] transition-transform duration-200 border border-gray-100 min-h-[420px] mx-auto">
                <div class="relative flex-shrink-0">
                    <div class="w-full aspect-[4/3] bg-white flex items-center justify-center overflow-hidden">
                        <img
                            src="{{ $product->image_path ? asset('storage/' . $product->image_path) : asset('storage/products/default.png') }}"
                            alt="{{ $product->name }}"
                            class="object-contain w-full h-full"
                        />
                        <div class="absolute top-2 left-2 flex flex-col gap-2 z-10">
                            @if($product->is_vegan)
                                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full shadow mb-1">🌱 Wegańskie</span>
                            @elseif($product->is_vegetarian)
                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full shadow">🥬 Wegetariańskie</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <h2 class="font-bold text-xl mb-1 text-gray-900">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->description, 120) }}</p>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-bold text-green-700">{{ number_format($product->price, 2) }} zł</span>
                        <span class="text-xs text-gray-500">/ dzień</span>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-yellow-500 text-base flex items-center h-6">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= round($product->reviews_avg_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                            @endfor
                        </span>
                        <span class="text-sm text-gray-700 ml-1">{{ number_format($product->reviews_avg_rating ?? 0, 1) }}/5</span>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex flex-col gap-2 mt-3">
                        <form method="POST"
                            action="{{ route('client.cart.add', $product) }}"
                            x-data="{ added: false }"
                            x-on:submit.prevent="
                                fetch($el.action, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ quantity: 1 })
                                }).then(res => {
                                    if (res.ok) {
                                        added = true;
                                        setTimeout(() => added = false, 3000);
                                    }
                                });
                            ">
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
                                ➕ Dodaj do koszyka
                            </button>
                        </form>
                        <button
                            @click="showModal = true; modalProduct = {{ $product->id }}"
                            class="w-full bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-lg font-semibold shadow mt-1"
                            type="button"
                        >
                            Zobacz opinie o produkcie
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-600">Brak wyników dla wybranych filtrów.</p>
        @endforelse

        {{-- MODAL Z OPINIAMI --}}
        <template x-if="showModal">
            <div
                x-show="showModal"
                x-transition.opacity
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center"
                style="background: rgba(0,0,0,0.5);"
            >
                <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative flex flex-col max-h-[80vh] overflow-y-auto">
                    <div class="sticky top-0 left-0 right-0 z-10 bg-white flex justify-end pb-2" style="margin:-1.5rem -1.5rem 1rem -1.5rem;">
                        <button
                            @click="showModal = false"
                            class="text-2xl text-gray-500 hover:text-blue-600 focus:outline-none"
                            aria-label="Zamknij"
                            style="padding: 0.5rem 1rem;"
                        >✖</button>
                    </div>
                    @if($productsById->count())
                        @foreach($productsById as $p)
                            <div x-show="modalProduct === {{ $p->id }}">
                                <h3 class="text-lg font-bold mb-4 text-center">Opinie o produkcie: {{ $p->name }}</h3>
                                @forelse ($p->reviews as $review)
                                    <div class="border-b border-gray-200 pb-3 mb-3 last:border-none last:mb-0">
                                        <p class="font-semibold text-sm">
                                            {{ $review->user ? $review->user->first_name . ' ' . $review->user->last_name : 'Anonim' }}
                                        </p>
                                        <div class="flex items-center h-6 mb-1">
                                            @for ($i = 0; $i < 5; $i++)
                                                <span class="text-2xl {{ $i < $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                            @endfor
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-700 text-sm whitespace-pre-line">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center">Brak opinii.</p>
                                @endforelse
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </template>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
