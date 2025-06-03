<x-guest-layout>
    <h2 class="text-xl font-semibold mb-4">Ustaw nowe hasło</h2>

    <!-- Communicates -->
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

    <!-- Form -->
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adres e-mail</label>
        <input type="email" name="email" id="email"
               class="block w-full border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500"
               placeholder="Email" required>

        <label for="password" class="block mt-4 text-sm font-medium text-gray-700 mb-1">Nowe hasło</label>
        <input type="password" name="password" id="password"
               class="block w-full border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500"
               placeholder="Nowe hasło" required>

        <label for="password_confirmation" class="block mt-4 text-sm font-medium text-gray-700 mb-1">Potwierdź hasło</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="block w-full border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500"
               placeholder="Potwierdź hasło" required>

        <button type="submit"
                class="mt-6 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
            Zmień hasło
        </button>
    </form>
</x-guest-layout>
