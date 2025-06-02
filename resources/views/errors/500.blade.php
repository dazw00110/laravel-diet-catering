{{-- resources/views/errors/500.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Server Error</title>
  @vite('resources/css/app.css')
</head>
<body class="h-full">
<main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
  <div class="text-center">
    <p class="text-base font-semibold text-indigo-600">500</p>
    <h1 class="mt-4 text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">Coś się przypaliło w naszej kuchni!</h1>
    <p class="mt-6 text-lg leading-7 text-gray-600">Ups, coś poszło nie tak na naszych serwerach.</p>
    <div class="mt-10 flex items-center justify-center gap-x-6">
      <a href="{{ url('/') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">Powrót na stronę główną</a>
    </div>
  </div>
</main>
</body>
</html>
