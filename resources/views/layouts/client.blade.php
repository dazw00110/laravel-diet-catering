<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CateringApp – @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col pt-[70px]">

    <!-- 🔝 STICKY NAVBAR -->
    <header class="bg-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center w-full">
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

            <nav class="ml-auto space-x-4 text-sm md:text-base">
                <a href="{{ route('client.dashboard') }}" class="hover:text-green-600">Strona główna</a>
                <a href="{{ route('client.products.index') }}" class="hover:text-green-600">Oferty</a>
                <a href="{{ route('client.contact') }}" class="hover:text-green-600">Kontakt</a>
                <a href="{{ route('client.orders.index') }}" class="hover:text-green-600">Moje zamówienia</a>
                <a href="{{ route('profile.show') }}" class="hover:text-green-600">Moje konto</a>
                <a href="{{ route('client.cart.index') }}" class="hover:text-green-600">Koszyk</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-red-600">Wyloguj</button>
                </form>
            </nav>
        </div>
    </header>

    <!-- 🔽 MAIN CONTENT -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- 🔚 FOOTER -->
    <footer class="bg-white border-t mt-10 text-center py-4 text-sm text-gray-500">
        &copy; {{ date('Y') }} CateringApp. Wszelkie prawa zastrzeżone.
    </footer>
</body>
</html>
