<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FitBox Catering – logowanie</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .overlay {
            background-color: rgba(255, 248, 240, 0.95);
            backdrop-filter: blur(5px);
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .header-overlay {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 1rem 2rem;
            border-radius: 0.75rem;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-8 text-gray-900">

    <!-- Catering name -->
    <div class="mb-6 text-center header-overlay text-white">
        <h1 class="text-4xl font-extrabold drop-shadow-md">
            FitBox Catering
        </h1>
        <p class="mt-1 text-sm tracking-wide">
            Twoja codzienna dawka zdrowia 🍰
        </p>
    </div>

    <!-- Form -->
    <div class="w-full max-w-xl overlay">
        {{ $slot }}


    </div>

</body>
</html>
