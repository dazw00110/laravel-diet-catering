<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700">Imię i nazwisko</label>
    <input id="name" name="name" type="text"
           value="{{ old('name', optional($user)->name) }}"
           required
           class="input input-bordered w-full">
    @error('name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
    <input id="email" name="email" type="email"
           value="{{ old('email', optional($user)->email) }}"
           required
           class="input input-bordered w-full">
    @error('email')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

@if (!isset($user))
<div class="mb-4">
    <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
    <input id="password" name="password" type="password"
           required
           class="input input-bordered w-full">
    @error('password')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potwierdź hasło</label>
    <input id="password_confirmation" name="password_confirmation" type="password"
           required
           class="input input-bordered w-full">
</div>
@endif

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

<div class="mt-6">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Zapisz
    </button>
</div>
