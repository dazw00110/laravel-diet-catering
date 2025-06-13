@extends('layouts.main')

@section('title', 'Oferty cateringowe')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showModal: false, modalProduct: null }">
    <h1 class="text-3xl font-bold mb-6">🥗 Oferty cateringowe</h1>

    <form method="GET" class="bg-white p-4 rounded shadow mb-6 flex flex-wrap gap-4 items-end">
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
            <label class="block text-sm font-medium">Rodzaj diety</label>
            <select name="diet" class="input input-bordered w-full">
                <option value="">Wszystkie</option>
                <option value="vegan" @selected(request('diet') === 'vegan')>Wegańskie</option>
                <option value="vegetarian" @selected(request('diet') === 'vegetarian')>Wegetariańskie</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-white">.</label>
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Filtruj</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2 auto-rows-fr" x-data="{ showModal: false, modalProduct: null }">
        @php
            $productsById = $products->keyBy('id');
        @endphp
        @forelse ($products as $product)
            <div class="w-full xl:w-[280px] bg-white shadow-lg rounded-2xl overflow-hidden flex flex-col h-full border border-gray-100 min-h-[420px] mx-auto">
                <div class="relative flex-shrink-0">
                    <div class="w-full aspect-[4/3] bg-white flex items-center justify-center overflow-hidden">
                        <img
                        src="{{ $product->image_url }}"
                        onerror="this.onerror=null;this.src='https://images.unsplash.com/vector-1738926381356-a78ac6592999?q=80&w=1160&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';"
                        alt="{{ $product->name }}"
                        class="object-contain w-full h-full"
                    />

                    </div>

                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <h2 class="font-bold text-lg mb-1">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($product->description, 100) }}</p>
                    <div class="flex items-center gap-2 mb-2">
                        @if($product->hasActivePromotion())
    <div class="flex items-center gap-2">
        <span class="text-gray-400 line-through">{{ number_format($product->price, 2) }} zł</span>
        <span class="text-lg font-bold text-green-700">{{ number_format($product->promotion_price, 2) }} zł</span>
    </div>
@else
    <span class="text-lg font-bold text-green-700">{{ number_format($product->price, 2) }} zł</span>
@endif
                        <span class="text-xs text-gray-500">/ dzień</span>
                    </div>
                    <div class="flex gap-2 mt-1 text-xs text-gray-700 mb-2">
                        @if($product->is_vegan)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">🌱 Wegańska</span>
                        @endif
                        @if($product->is_vegetarian)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">🥬 Wegetariańska</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 mb-2">
                        @php
                            $avg = $product->reviews->count() > 0
                                ? $product->reviews->avg('rating')
                                : null;
                        @endphp
                        <span class="text-yellow-500 text-base flex items-center h-6">
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
                        <div class="text-xs text-gray-500 italic mb-2">
                            "{{ Str::limit($review->comment, 60) }}" – Anonim
                        </div>
                    @endif
                    <div class="flex flex-col gap-2 mt-auto">
                        <a href="{{ route('login') }}"
                           class="w-full block text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm font-semibold shadow transition">
                            ➕ Dodaj do koszyka
                        </a>
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

        <!-- Modal for product reviews -->
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
                    @foreach($productsById as $p)
                        <div x-show="modalProduct === {{ $p->id }}">
                            <h3 class="text-lg font-bold mb-4 text-center">Opinie o produkcie: {{ $p->name }}</h3>
                            @forelse ($p->reviews as $review)
                                <div class="border-b border-gray-200 pb-3 mb-3 last:border-none last:mb-0">
                                    <p class="font-semibold text-sm">
                                        Anonim
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
                </div>
            </div>
        </template>
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
