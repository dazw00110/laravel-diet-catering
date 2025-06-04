@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Aktualizuj użytkownika</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block">Imie</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Nazwisko</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div>
            <label class="block">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="w-full border border-gray-300 rounded px-4 py-2" required>
        </div>

        <div class="flex items-center gap-6 mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegetarian" id="is_vegetarian" value="1" class="rounded"
                    {{ old('is_vegetarian', $user->is_vegetarian) ? 'checked' : '' }}>
                <span>Wegetarianin</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_vegan" id="is_vegan" value="1" class="rounded"
                    {{ old('is_vegan', $user->is_vegan) ? 'checked' : '' }}>
                <span>Weganin</span>
            </label>
        </div>

        <div>
            <label class="block">Role</label>
            <select name="user_type_id" class="w-full border border-gray-300 rounded px-4 py-2" required>
                <option value="1" @selected($user->user_type_id == 1)>Admin</option>
                <option value="2" @selected($user->user_type_id == 2)>Klient</option>
                <option value="3" @selected($user->user_type_id == 3)>Pracownik</option>
            </select>
        </div>

        <div>
            <label class="block">Nowe hasło(opcjonalne)</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <div>
            <label class="block">Podtwierdź nowe hasło</label>
            <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded px-4 py-2">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">Zaktualizuj</button>
    </form>
</div>
@endsection
