@extends('layouts.staff')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">Aktualizuj dane klienta</h1>

    {{-- Komunikat sukcesu --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Komunikaty błędów --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.users.update', $user->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block">Imię</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Nazwisko</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                   class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">E-mail</label>
            <input type="email" value="{{ $user->email }}"
                class="w-full border border-gray-200 bg-gray-100 rounded px-4 py-2 cursor-not-allowed" disabled>
        </div>


        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegetarian" value="1"
                       {{ old('is_vegetarian', $user->is_vegetarian) ? 'checked' : '' }}>
                <span>Wegetarianin</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegan" value="1"
                       {{ old('is_vegan', $user->is_vegan) ? 'checked' : '' }}>
                <span>Weganin</span>
            </label>
        </div>

        <div>
            <label class="block">Nowe hasło (opcjonalnie)</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <div>
            <label class="block">Potwierdź hasło</label>
            <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Zaktualizuj
        </button>
    </form>
</div>
@endsection
