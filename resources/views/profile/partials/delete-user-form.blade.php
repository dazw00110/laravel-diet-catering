<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Usuń konto
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Po usunięciu konta wszystkie dane zostaną trwale usunięte.
        </p>
    </header>

    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')

        <div>
            <x-input-label for="password" value="Hasło" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        @if ($errors->has('userDeletion'))
            <p class="text-red-600 text-sm mt-2">
                {{ $errors->first('userDeletion') }}
            </p>
        @endif


        <div class="flex justify-start mt-4">
            <x-danger-button>
                Usuń konto
            </x-danger-button>
        </div>
    </form>
</section>
