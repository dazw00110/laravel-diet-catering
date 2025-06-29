@extends('layouts.staff')

@section('title', 'Lista produktów')

@section('content')
<style>
.promotion-price {
    position: relative;
    display: inline-block;
    color: #22c55e !important;
    font-weight: bold;
}

.tooltip-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.tooltip {
    position: absolute;
    z-index: 10;
    width: 18rem;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    text-align: left;
    color: #1f2937;
    background-color: white;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s, visibility 0.2s;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
}

.tooltip-container:hover .tooltip {
    opacity: 1;
    visibility: visible;
}

.tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: white;
}

.expired-promotion {
    color: #ef4444 !important;
    text-decoration: line-through;
}

.blocked-edit-button {
    position: relative;
    display: inline-block;
    cursor: not-allowed;
}

.info-icon {
    color: #2563eb;
    font-weight: bold;
    font-size: 1.125rem;
    margin-left: 0.25rem;
}

.price-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
}
</style>

<div class="mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
        <div>
            <label class="block text-sm mb-1">Nazwa produktu</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Nazwa" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Cena od</label>
            <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" placeholder="Cena od (zł)" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Cena do</label>
            <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" placeholder="Cena do (zł)" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Kcal od</label>
            <input type="number" step="1" name="min_calories" value="{{ request('min_calories') }}" placeholder="Kcal min" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Kcal do</label>
            <input type="number" step="1" name="max_calories" value="{{ request('max_calories') }}" placeholder="Kcal max" class="input input-bordered w-full">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_vegetarian" id="is_vegetarian" value="1" {{ request('is_vegetarian') ? 'checked' : '' }} class="rounded">
            <label for="is_vegetarian" class="text-sm">Wegetariańskie</label>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_vegan" id="is_vegan" value="1" {{ request('is_vegan') ? 'checked' : '' }} class="rounded">
            <label for="is_vegan" class="text-sm">Wegańskie</label>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="non_vegan_vegetarian" id="non_vegan_vegetarian" value="1" {{ request('non_vegan_vegetarian') ? 'checked' : '' }} class="rounded">
            <label for="non_vegan_vegetarian" class="text-sm">Bez zastrzeżeń do diety</label>
        </div>

        <div class="col-span-full flex gap-2 mt-2">
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Filtruj</button>
            <a href="{{ route('staff.products.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>
        </div>
    </form>
</div>

@php
    $sort = request('sort');
    $dir = request('dir') === 'desc' ? 'asc' : 'desc';

    function generate_sort_url($field) {
        $currentSort = request('sort');
        $currentDir = request('dir') === 'desc' ? 'desc' : 'asc';
        $nextDir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $nextDir]);
    }

    function sort_icon($field) {
        $currentSort = request('sort');
        $currentDir = request('dir') ?? 'asc';
        if ($currentSort === $field) {
            return $currentDir === 'desc' ? '↓' : '↑';
        }
        return '';
    }

    function formatTimeRemaining($expiresAt) {
        if (!$expiresAt) return '';

        $now = now();
        $expires = \Carbon\Carbon::parse($expiresAt);

        if ($expires->isPast()) {
            return 'Promocja wygasła';
        }

        $diff = $now->diff($expires);

        if ($diff->days > 0) {
            return $diff->days . ' dni ' . $diff->h . ' godzin';
        } elseif ($diff->h > 0) {
            return $diff->h . ' godzin ' . $diff->i . ' minut';
        } else {
            return $diff->i . ' minut';
        }
    }

    // Generate URL with current parameters for promotion links
    function generate_promotion_url($route, $product) {
        $currentParams = request()->query();
        return route($route, $product) . '?' . http_build_query($currentParams);
    }
@endphp

<table class="min-w-full bg-white shadow rounded">
    <thead>
        <tr>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('id') }}" class="hover:underline">ID {!! sort_icon('id') !!}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('name') }}" class="hover:underline">Nazwa {!! sort_icon('name') !!}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('price') }}" class="hover:underline">Cena {!! sort_icon('price') !!}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('calories') }}" class="hover:underline">Kcal {!! sort_icon('calories') !!}</a></th>
            <th class="p-2 border-b text-center">Wegetariański</th>
            <th class="p-2 border-b text-center">Wegański</th>
            <th class="p-2 border-b text-center">Akcje</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            @php
                $hasActivePromotion = $product->promotion_price &&
                                    $product->promotion_expires_at &&
                                    now()->lt($product->promotion_expires_at);
                $hasExpiredPromotion = $product->promotion_price &&
                                     $product->promotion_expires_at &&
                                     now()->gte($product->promotion_expires_at);
            @endphp
            <tr>
                <td class="p-2 border-b text-center">{{ $product->id }}</td>
                <td class="p-2 border-b text-center">{{ $product->name }}</td>
                <td class="p-2 border-b text-center">
                    @if($hasActivePromotion)
                        <div class="price-display">
                            <span class="promotion-price">{{ number_format($product->promotion_price, 2) }} zł</span>
                            <div class="tooltip-container">
                                <span class="info-icon">ℹ️</span>
                                <div class="tooltip">
                                    <p><strong>Informacje o promocji:</strong></p>
                                    <ul class="list-disc list-inside mt-1">
                                        <li><strong>Cena oryginalna:</strong> {{ number_format($product->price, 2) }} zł</li>
                                        <li><strong>Pozostało:</strong> {{ formatTimeRemaining($product->promotion_expires_at) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif($hasExpiredPromotion)
                        <div class="expired-promotion">
                            {{ number_format($product->price, 2) }} zł
                        </div>
                    @else
                        {{ number_format($product->price, 2) }} zł
                    @endif
                </td>
                <td class="p-2 border-b text-center">{{ $product->calories ?? '-' }}</td>
                <td class="p-2 border-b text-center">
                    @if ($product->is_vegetarian)
                        <span class="text-green-700 font-semibold">✔</span>
                    @else
                        <span class="text-gray-400">–</span>
                    @endif
                </td>
                <td class="p-2 border-b text-center">
                    @if ($product->is_vegan)
                        <span class="text-green-700 font-semibold">✔</span>
                    @else
                        <span class="text-gray-400">–</span>
                    @endif
                </td>
                <td class="p-2 border-b text-center space-y-1 space-x-1">
                    @if($hasActivePromotion)
                        <div class="tooltip-container blocked-edit-button">
                            <span class="bg-gray-400 text-white px-2 py-1 rounded text-sm cursor-not-allowed opacity-60">Edytuj</span>
                            <span class="info-icon">ℹ️</span>
                            <div class="tooltip">
                                <p><strong>Edycja zablokowana</strong></p>
                                <p class="mt-1">Aby edytować produkt, musisz najpierw usunąć aktywną promocję lub poczekać aż wygaśnie.</p>
                                <p class="mt-1"><strong>Promocja kończy się za:</strong> {{ formatTimeRemaining($product->promotion_expires_at) }}</p>
                            </div>
                        </div>
                    @else
                        <a href="{{ generate_promotion_url('staff.products.edit', $product) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">Edytuj</a>
                    @endif

                    @if($hasActivePromotion)
                        <a href="{{ generate_promotion_url('staff.products.promotion', $product) }}" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">Edytuj promocję</a>
                    @else
                        <a href="{{ generate_promotion_url('staff.products.promotion', $product) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">Ustal promocję</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="p-4 text-center text-gray-500">Brak wyników</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
