<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Dane profilu
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Zaktualizuj swoje dane osobowe i preferencje.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Imię --}}
        <div>
            <x-input-label for="first_name" :value="'Imię'" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full"
                :value="old('first_name', $user->first_name)" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        {{-- Nazwisko --}}
        <div>
            <x-input-label for="last_name" :value="'Nazwisko'" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full"
                :value="old('last_name', $user->last_name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>

    {{-- Email --}}
    @if ($user->user_type_id === 1)
        <div>
            <x-input-label for="email" :value="'Adres e-mail'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
    @else
        <div>
            <x-input-label for="email" :value="'Adres e-mail'" />
            <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="$user->email" disabled />
        </div>
    @endif


        {{-- Rola (podgląd) --}}
        <div>
            <x-input-label for="role" :value="'Rola użytkownika'" />
            <x-text-input id="role" type="text" class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="ucfirst($user->userType->name)" disabled />
        </div>

        {{-- Preferencje żywieniowe --}}
        <div class="flex gap-4">
            <label><input type="checkbox" name="is_vegetarian" value="1" {{ old('is_vegetarian', $user->is_vegetarian) ? 'checked' : '' }}> Wegetarianin</label>
            <label><input type="checkbox" name="is_vegan" value="1" {{ old('is_vegan', $user->is_vegan) ? 'checked' : '' }}> Weganin</label>
        </div>

        <div class="flex items-center gap-4 pb-4">
            <x-primary-button>Zapisz zmiany</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">Zapisano.</p>
            @endif
        </div>
    </form>
</section>
