<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel pracownika - @yield('title', 'Dashboard')</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            @php
                $dashboardRoute = match(auth()->user()?->user_type_id) {
                    1 => route('admin.dashboard'),
                    2 => route('client.dashboard'),
                    3 => route('staff.dashboard'),
                    default => '#'
                };
            @endphp

            <a href="{{ $dashboardRoute }}" class="text-xl font-bold text-gray-800 hover:text-blue-600">
                🍰 CateringApp
            </a>

            <div class="flex items-center gap-4 text-sm font-medium ml-auto">
                <a href="{{ route('staff.users.index') }}" class="hover:text-blue-600">Użytkownicy</a>
                <a href="{{ route('staff.orders.index') }}" class="hover:text-blue-600">Zamówienia</a>
                <a href="{{ route('staff.products.index') }}" class="hover:text-blue-600">Produkty</a>
                <a href="{{ route('staff.stats.index') }}" class="hover:text-blue-600">Statystyki</a>
                <a href="{{ route('profile.show') }}" class="hover:text-blue-600">Mój profil</a>

                <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline text-sm">Wyloguj</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">@yield('title')</h1>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
