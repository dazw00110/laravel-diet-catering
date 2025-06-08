<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel klienta - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex gap-4 text-sm font-medium">
                <a href="{{ route('client.dashboard') }}" class="hover:text-blue-600">Strona główna</a>
                <a href="{{ route('client.orders.index') }}" class="hover:text-blue-600">Moje zamówienia</a>
                <a href="{{ route('client.products.index') }}" class="hover:text-blue-600">Produkty</a>
                <a href="{{ route('profile.show') }}" class="hover:text-blue-600">Mój profil</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-red-500 hover:underline text-sm">Wyloguj</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">@yield('title')</h1>
        @yield('content')
    </main>
</body>
</html>
