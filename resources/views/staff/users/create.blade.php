@extends('layouts.staff')

@section('title', 'Dodaj nowego klienta')

@section('content')
<div class="p-6">
    <form action="{{ route('staff.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block mb-1 text-sm font-medium">Imię</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
            @error('first_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Nazwisko</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
            @error('last_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Data urodzenia</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
            @error('birth_date')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian') ? 'checked' : '' }} class="rounded">
                <span>Wegetarianin</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan') ? 'checked' : '' }} class="rounded">
                <span>Weganin</span>
            </label>
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Hasło</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Potwierdź hasło</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
            Stwórz
        </button>
    </form>
</div>
@endsection
