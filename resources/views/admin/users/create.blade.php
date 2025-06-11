@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Stwórz nowego użytkownika</h1>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block">Imie</label>
            <input type="text" name="first_name" class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Nazwisko</label>
            <input type="text" name="last_name" class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Email</label>
            <input type="email" name="email" class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Data urodzenia</label>
            <input
                type="date"
                name="birth_date"
                id="birth_date"
                value="{{ old('birth_date') }}"
                min="{{ now()->subYears(150)->toDateString() }}"
                max="{{ now()->toDateString() }}"
                class="input input-bordered w-full"
                required
            >
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <div class="flex items-center gap-6 mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegetarian" id="is_vegetarian" value="1" class="rounded">
                <span>Wegetarianin</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegan" id="is_vegan" value="1" class="rounded">
                <span>Weganin</span>
            </label>
        </div>

        <div>
            <label class="block">Role</label>
            <select name="user_type_id" class="w-full border border-gray-300 rounded px-4 py-2" required>
                <option value="1">Admin</option>
                <option value="2">Klient</option>
                <option value="3">Pracownik</option>
            </select>
        </div>

        <div>
            <label class="block">Hasło</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Podtwierdz hasło</label>
            <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded">Stwórz</button>

    </form>
</div>
@endsection
