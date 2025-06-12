{{-- Name --}}
<div>
    <label class="block mb-1 text-sm font-medium">Imię</label>
    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required
           class="w-full border border-gray-300 rounded px-4 py-2">
    @error('first_name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Surname --}}
<div>
    <label class="block mb-1 text-sm font-medium">Nazwisko</label>
    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required
           class="w-full border border-gray-300 rounded px-4 py-2">
    @error('last_name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Prefferences --}}
<div class="flex items-center gap-6">
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian', $user->is_vegetarian ?? false) ? 'checked' : '' }} class="rounded">
        <span>Wegetarianin</span>
    </label>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan', $user->is_vegan ?? false) ? 'checked' : '' }} class="rounded">
        <span>Weganin</span>
    </label>
</div>

{{-- Password--}}
@if (isset($user))
<div>
    <label class="block mb-1 text-sm font-medium">Nowe hasło (opcjonalne)</label>
    <input type="password" name="password" class="w-full border border-gray-300 rounded px-4 py-2">
    @error('password')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block mb-1 text-sm font-medium">Potwierdź hasło</label>
    <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded px-4 py-2">
</div>
@else
<div>
    <label class="block mb-1 text-sm font-medium">Hasło</label>
    <input type="password" name="password" required class="w-full border border-gray-300 rounded px-4 py-2">
</div>

<div>
    <label class="block mb-1 text-sm font-medium">Potwierdź hasło</label>
    <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded px-4 py-2">
</div>
@endif
