@extends('layouts.admin')

@section('title', 'Lista produktów')

@section('content')
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
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>
        </div>
    </form>
</div>

<div class="mb-4 text-right">
    <a href="{{ route('admin.products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Dodaj produkt</a>
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
        $currentDir = request('dir') === 'desc' ? 'desc' : 'asc';
        if ($currentSort === $field) {
            return $currentDir === 'desc' ? '↓' : '↑';
        }
        return '';
    }
@endphp

<table class="min-w-full bg-white shadow rounded">
    <thead>
        <tr>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('category_id') }}" class="hover:underline">Kategoria ID {{ sort_icon('category_id') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('name') }}" class="hover:underline">Nazwa {{ sort_icon('name') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('price') }}" class="hover:underline">Cena {{ sort_icon('price') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('calories') }}" class="hover:underline">Kcal {{ sort_icon('calories') }}</a></th>
            <th class="p-2 border-b text-center">Wegetariański</th>
            <th class="p-2 border-b text-center">Wegański</th>
            <th class="p-2 border-b text-center">Akcje</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
            <tr>
                <td class="p-2 border-b text-center">{{ $product->category_id ?? '-' }}</td>
                <td class="p-2 border-b text-center">{{ $product->name }}</td>
                <td class="p-2 border-b text-center">{{ $product->price }} zł</td>
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
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">Edytuj</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Na pewno?')" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">Usuń</button>
                    </form>
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
