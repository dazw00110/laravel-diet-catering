@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow-xl rounded-2xl">
    <h2 class="text-2xl font-bold mb-4">Konfiguracja uwierzytelniania dwuskładnikowego</h2>
    <p class="mb-2">Zeskanuj ten kod QR w swojej aplikacji TOTP (np. Google Authenticator):</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qr) }}&amp;size=200x200" alt="TOTP QR">
    <div class="mt-4">
        <a href="{{ route('2fa.verify') }}" class="text-blue-500 hover:underline">Już zeskanowałeś? Zferyfikuj kod tutaj</a>
    </div>
</div>
@endsection
