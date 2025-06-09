{{-- Imię --}}
<div class="mb-4">
    <label for="first_name" class="block text-sm font-medium text-gray-700">Imię</label>
    <input id="first_name" name="first_name" type="text"
           value="{{ old('first_name', optional($user)->first_name) }}"
           required
           class="input input-bordered w-full">
    @error('first_name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Nazwisko --}}
<div class="mb-4">
    <label for="last_name" class="block text-sm font-medium text-gray-700">Nazwisko</label>
    <input id="last_name" name="last_name" type="text"
           value="{{ old('last_name', optional($user)->last_name) }}"
           required
           class="input input-bordered w-full">
    @error('last_name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Preferencje dietetyczne --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Preferencje dietetyczne</label>
    <div class="flex gap-6">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian', optional($user)->is_vegetarian) ? 'checked' : '' }}>
            <span class="ml-2">Wegetariańska</span>
        </label>
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan', optional($user)->is_vegan) ? 'checked' : '' }}>
            <span class="ml-2">Wegańska</span>
        </label>
    </div>
</div>

{{-- Nowe hasło --}}
<div class="mb-4">
    <label for="password" class="block text-sm font-medium text-gray-700">Nowe hasło</label>
    <input id="password" name="password" type="password"
           class="input input-bordered w-full">
    @error('password')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Potwierdzenie hasła --}}
<div class="mb-4">
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potwierdź hasło</label>
    <input id="password_confirmation" name="password_confirmation" type="password"
           class="input input-bordered w-full">
</div>

{{-- Przycisk --}}
<div class="mt-6">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Zapisz zmiany
    </button>
</div>
