<x-guest-layout>

    <!-- Komunikaty -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded p-3">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
            <div class="space-x-2">
                <a href="{{ route('password.request') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 no-underline">
                    Resetuj hasło
                </a>
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
            <a href="{{ route('register') }}" class="text-green-700 font-semibold hover:text-green-800 no-underline">
                Zarejestruj się
            </a>
        </p>
    </div>

    <!-- Powrót na stronę główną -->
    <div class="text-center mt-3">
        <a href="{{ route('home') }}"
           class="text-sm text-green-700 hover:text-green-800 no-underline font-medium">
            Powrót na stronę główną
        </a>
    </div>

</x-guest-layout>
