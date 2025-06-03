<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zweryfikuj kod TOTP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Strona główna</a>
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-12 px-4">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
            <h1 class="text-2xl font-bold text-center mb-4">Zweryfikuj kod TOTP</h1>

            <form method="POST" action="{{ url('/2fa/verify') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Kod uwierzytelniający</label>
                    <input type="text" name="code" id="code" inputmode="numeric"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required autocomplete="one-time-code">
                    @error('code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-black text-white px-4 py-2 rounded-md hover:bg-gray-800 transition">
                    ZWERYFIKUJ
                </button>
            </form>
        </div>
    </main>

</body>
</html>
