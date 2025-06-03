@extends('layouts.admin')

@section('title', 'Dodaj produkt')

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

<form action="{{ route('admin.products.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-gray-700">Nazwa</label>
            <input type="text" name="name" placeholder="Dieta" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('name') }}" required>
        </div>

        <div>
            <label class="block text-gray-700">Cena (zł)</label>
            <input type="number" step="0.01" name="price" placeholder="00.00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('price') }}" required>
        </div>

        <div>
            <label class="block text-gray-700">Kalorie</label>
            <input type="number" name="calories" placeholder="1234" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('calories') }}">
        </div>

        <div>

        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegan" class="rounded" value="1" {{ old('is_vegan') ? 'checked' : '' }}> <span class="ml-2">Wegański</span>
            </label>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegetarian" class="rounded" value="1" {{ old('is_vegetarian') ? 'checked' : '' }}> <span class="ml-2">Wegetariański</span>
            </label>
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-gray-700">Opis</label>
        <textarea name="description" rows="4" placeholder="Opis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
    </div>

    <div class="mt-6">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Zapisz</button>
        <a href="{{ route('admin.products.index') }}" class="ml-4 text-gray-700 hover:underline">Anuluj</a>
    </div>
</form>
@endsection
