<x-guest-layout>
    <!-- Komunikat sesji -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Adres e-mail -->
        <div>
            <x-input-label for="email" :value="'Adres e-mail'" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Hasło -->
        <div class="mt-4">
            <x-input-label for="password" :value="'Hasło'" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Zapamiętaj mnie -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                       name="remember">
                <span class="ml-2 text-sm text-gray-700">Zapamiętaj mnie</span>
            </label>
        </div>

        <!-- Akcje -->
        <div class="flex items-center justify-between mt-6">
            <!-- Przyciski -->
            <div class="space-x-2">
                <button type="button"
                        onclick="alert('Funkcja resetowania hasła będzie dostępna wkrótce 😊')"
                        class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Resetuj hasło
                </button>
            </div>

            <x-primary-button>
                Zaloguj się
            </x-primary-button>
        </div>
    </form>

    <!-- Rejestracja -->
    <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
            Nie masz konta?
            <a href="{{ route('register') }}" class="text-green-700 font-semibold hover:underline">
                Zarejestruj się
            </a>
        </p>
    </div>
</x-guest-layout>
