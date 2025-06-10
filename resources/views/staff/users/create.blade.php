
@extends('layouts.staff')

@section('title', 'Dodaj nowego klienta')

@section('content')
<div class="p-4">
    <h1 class="text-xl font-semibold mb-4">Dodaj nowego klienta</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm">Imię</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block text-sm">Nazwisko</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block text-sm">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block text-sm">Data urodzenia</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div class="flex gap-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian') ? 'checked' : '' }}>
                <span class="ml-2">Wegetarianin</span>
            </label>

            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan') ? 'checked' : '' }}>
                <span class="ml-2">Weganin</span>
            </label>
        </div>

        <div>
            <label class="block text-sm">Hasło</label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block text-sm">Potwierdź hasło</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Stwórz</button>
    </form>
</div>
@endsection
