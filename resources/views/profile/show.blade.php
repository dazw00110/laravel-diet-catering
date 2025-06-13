@extends($layout)

@section('title', 'Mój profil')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Dane konta</h2>

        <p><strong>Imię:</strong> {{ $user->first_name }}</p>
        <p><strong>Nazwisko:</strong> {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Rola:</strong> {{ $user->userType->name }}</p>

        @if ($user->is_vegetarian)
            <p>✅ Wegetarianin</p>
        @endif
        @if ($user->is_vegan)
            <p>✅ Weganin</p>
        @endif
@if (!empty($user->totp_secret))
    <p class="mt-4 text-green-600 font-semibold">🔒 Uwierzytelnianie dwuskładnikowe (TOTP) jest WŁĄCZONE.</p>
@else
    <p class="mt-4 text-red-600 font-semibold">⚠️ Uwierzytelnianie dwuskładnikowe (TOTP) jest WYŁĄCZONE.</p>
    <a href="{{ route('profile.totp.setup') }}"
   class="inline-block mt-4 px-4 py-2 bg-yellow-400 text-black rounded hover:bg-yellow-500">
   Włącz teraz
</a>
@endif

        <a href="{{ route('profile.edit') }}"
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edytuj profil
        </a>
    </div>
@endsection
