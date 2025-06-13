@extends('layouts.app')

@section('title', '2FA Setup')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-center mb-4">Konfiguracja uwierzytelniania dwuskładnikowego</h1>
        <p class="text-sm text-gray-700 mb-4 text-center">
            Zeskanuj ten kod QR w aplikacji TOTP (np. Google Authenticator), aby powiązać konto.
        </p>

        <div class="flex justify-center mb-6">
            <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qr) }}&size=200x200"
                 alt="Kod QR TOTP" class="rounded shadow-md">
        </div>

        <p class="text-center text-sm">
            <a href="{{ route('2fa.verify') }}" class="text-blue-600 hover:underline">
                Już zeskanowane? Zweryfikuj kod tutaj
            </a>
        </p>
    </div>
@endsection
