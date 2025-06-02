<x-guest-layout>
    <h2 class="text-xl font-semibold mb-4">Reset Hasła</h2>

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

    <!-- Formularz -->
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adres e-mail</label>
        <input type="email" name="email" id="email"
               class="block w-full border-gray-300 rounded shadow-sm focus:ring-green-500 focus:border-green-500"
               placeholder="Twój email" required>

        <button type="submit"
                class="mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
            Wyślij token
        </button>
    </form>
</x-guest-layout>
