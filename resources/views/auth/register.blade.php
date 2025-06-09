<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('Imię')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Nazwisko')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Adres email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Birth Date -->
        <div class="mt-4">
            <x-input-label for="birth_date" :value="__('Data urodzenia')" />
            <input
                type="date"
                id="birth_date"
                name="birth_date"
                value="{{ old('birth_date') }}"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                min="{{ now()->subYears(150)->toDateString() }}"
                max="{{ now()->subYears(14)->toDateString() }}"
                required
            />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <!-- Vegan -->
        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegan" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_vegan') ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Jestem weganinem</span>
            </label>
        </div>

        <!-- Vegetarian -->
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_vegetarian" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_vegetarian') ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">Jestem wegetarianinem</span>
            </label>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Hasło')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            {{-- Dodajemy opis wymagań hasła --}}
            <p class="text-sm text-gray-500 mt-1">
                Hasło musi mieć minimum 8 znaków, zawierać literę i cyfrę.
            </p>
        </div>


        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Potwierdź hasło')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Masz już konto?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Zarejestruj się') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('#birth_date');

            input.addEventListener('invalid', (e) => {
                if (input.validity.rangeOverflow) {
                    input.setCustomValidity("Użytkownik musi mieć co najmniej 14 lat.");
                } else if (input.validity.rangeUnderflow) {
                    input.setCustomValidity("Data urodzenia nie może wskazywać wieku powyżej 150 lat.");
                } else {
                    input.setCustomValidity("");
                }
            });

            input.addEventListener('input', () => {
                input.setCustomValidity("");
            });
        });
    </script>

</x-guest-layout>
