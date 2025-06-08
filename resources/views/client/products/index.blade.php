@extends('layouts.client')

@section('title', 'Oferty cateringowe')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showMessage: false }">
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
            <label class="block text-sm font-medium text-white">.</label>
            <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Filtruj</button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white shadow rounded overflow-hidden flex flex-col">
                <img
                    src="{{ $product->image_url ?: asset('storage/default-product.png') }}"
                    alt="{{ $product->name }}"
                    class="w-full h-48 object-contain bg-white"
                >
                <div class="p-4 flex flex-col justify-between flex-grow">
                    <div class="flex flex-col justify-between h-full">
                        <div class="mb-2">
                            <h2 class="font-bold text-lg mb-1">{{ $product->name }}</h2>
                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($product->description, 100) }}</p>
                        </div>
                        <div class="mt-auto">
                            <p class="text-sm text-gray-800 mb-1">
                                Cena: <strong>{{ number_format($product->price, 2) }} zł / dzień</strong>
                            </p>
                            <p class="text-sm text-gray-800 mb-1">
                                Wegan: {!! $product->is_vegan ? '✅' : '❌' !!},
                                Wegetariański: {!! $product->is_vegetarian ? '✅' : '❌' !!}
                            </p>
                            <p class="text-sm text-gray-800 mb-2">
                                Ocena: {{ number_format($product->reviews_avg_rating ?? 0, 1) }} / 5
                            </p>

                            <div class="flex justify-between items-center gap-2">
                                <div class="w-full" x-data="{ added: false }">
                                    <form method="POST"
                                        action="{{ route('client.cart.add', $product) }}"
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
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded w-full text-sm">
                                            ➕ Dodaj do koszyka
                                        </button>
                                    </form>

                                    <div x-show="added"
                                        x-transition
                                        class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50"
                                        x-cloak>
                                        ✅ Dodano do koszyka!
                                        <a href="{{ route('client.cart.index') }}" class="underline ml-2">Zobacz koszyk</a>
                                    </div>
                                </div>

                                <div x-data="{ showModal: false }" class="w-full">
                                    <button
                                        @click="showModal = true"
                                        class="text-blue-600 hover:underline text-sm w-full text-left"
                                    >
                                        Zobacz opinie o produkcie
                                    </button>

                                    <div
                                        x-show="showModal"
                                        x-transition
                                        @click.away="showModal = false"
                                        x-cloak
                                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                                    >
                                        <div class="bg-white rounded shadow-lg max-w-md w-full p-6 overflow-y-auto max-h-[70vh]" @click.stop>
                                            <h3 class="text-lg font-bold mb-4">Opinie o produkcie</h3>
                                            @forelse ($product->reviews as $review)
                                                <div class="border-b border-gray-200 pb-3 mb-3 last:border-none last:mb-0">
                                                    <p class="font-semibold text-sm">
{{ $review->user ? $review->user->first_name . ' ' . $review->user->last_name : 'Anonim' }}

                                                    </p>
                                                    <p class="text-yellow-500 text-sm mb-1">
                                                        @for ($i = 0; $i < 5; $i++)
                                                            @if ($i < $review->rating)
                                                                ⭐
                                                            @else
                                                                ☆
                                                            @endif
                                                        @endfor
                                                    </p>
                                                    <p class="text-gray-700 text-sm whitespace-pre-line">{{ $review->comment }}</p>
                                                </div>
                                            @empty
                                                <p class="text-gray-500">Brak opinii.</p>
                                            @endforelse

                                            <div class="text-right mt-4">
                                                <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="showModal = false">
                                                    Zamknij
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /modal -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-600">Brak wyników dla wybranych filtrów.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
