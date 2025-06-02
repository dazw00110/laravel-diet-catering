{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forbidden</title>
  @vite('resources/css/app.css')
</head>
<body class="h-full">
<main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
  <div class="text-center">
    <p class="text-base font-semibold text-indigo-600">403</p>
    <h1 class="mt-4 text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">Brak dostępu do tej kuchni!</h1>
    <p class="mt-6 text-lg leading-7 text-gray-600">Nie masz uprawnień do przeglądania tej strony.</p>
    <div class="mt-10 flex items-center justify-center gap-x-6">
      <a href="{{ url('/') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Powrót do strony głównej</a>
    </div>
  </div>
</main>
</body>
</html>
