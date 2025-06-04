@extends('layouts.staff')

@section('title', 'Edytuj produkt')

@section('content')

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('staff.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-gray-700">Nazwa</label>
            <input
                type="text"
                name="name"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                value="{{ old('name', $product->name) }}"
                required
            >
        </div>

        <div>
            <label class="block text-gray-700">Cena (zł)</label>
            <input
                type="number"
                step="0.01"
                name="price"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                value="{{ old('price', $product->price) }}"
                required
            >
        </div>

        <div>
            <label class="block text-gray-700">Kalorie</label>
            <input
                type="number"
                name="calories"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                value="{{ old('calories', $product->calories) }}"
            >
        </div>

        <div>
            <label class="block text-gray-700">Zdjęcie produktu</label>
            <input
                type="file"
                name="image"
                accept="image/*"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
            >

            @if ($product->image_path)
                <div class="mt-2">
                    <p class="text-gray-600 text-sm mb-1">Aktualne zdjęcie:</p>
                    <img
                        src="{{ asset('storage/' . $product->image_path) }}"
                        alt="Zdjęcie produktu"
                        class="w-32 h-32 object-cover rounded"
                    >
                </div>
            @endif
        </div>

        <div>
            <label class="inline-flex items-center">
                <input
                    type="checkbox"
                    name="is_vegan"
                    class="rounded"
                    value="1"
                    {{ old('is_vegan', $product->is_vegan) ? 'checked' : '' }}
                >
                <span class="ml-2">Wegański</span>
            </label>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input
                    type="checkbox"
                    name="is_vegetarian"
                    class="rounded"
                    value="1"
                    {{ old('is_vegetarian', $product->is_vegetarian) ? 'checked' : '' }}
                >
                <span class="ml-2">Wegetariański</span>
            </label>
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-gray-700">Opis</label>
        <textarea
            name="description"
            rows="4"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        >{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="mt-6">
        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
        >
            Zapisz zmiany
        </button>
        <a
            href="{{ route('staff.products.index') }}"
            class="ml-4 text-gray-700 hover:underline"
        >
            Anuluj
        </a>
    </div>
</form>

@endsection
