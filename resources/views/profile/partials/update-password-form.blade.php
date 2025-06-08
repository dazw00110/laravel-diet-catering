<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Zmień hasło
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Upewnij się, że używasz długiego, losowego hasła, aby zwiększyć bezpieczeństwo konta.
        </p>
    </header>

    @if (session('status') === 'password-updated')
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 8000)"
            x-transition
            class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50"
            style="display: none;"
        >
            ✅ Hasło zostało pomyślnie zmienione.
        </div>
    @endif

    @if ($errors->updatePassword->has('current_password'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 6000)"
            x-transition
            class="fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50"
            style="display: none;"
        >
            ❌ Nieprawidłowe hasło. Spróbuj ponownie.
        </div>
    @endif

    @if ($errors->updatePassword->has('password_confirmation'))
        <div ...>
            ❌ Hasła nie są zgodne.
        </div>
    @endif


    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" value="Aktualne hasło" />
            <x-text-input id="current_password" name="current_password" type="password"
                class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nowe hasło" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Potwierdź nowe hasło" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pb-4">
            <x-primary-button>Zapisz hasło</x-primary-button>
            <!-- Password change message next to button -->
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 9000)"
                   class="text-sm text-green-600">Hasło zostało zmienione.</p>
            @endif
        </div>
    </form>
</section>
