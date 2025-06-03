@if(auth()->check() && is_null(auth()->user()->totp_secret))
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-xl shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-semibold text-sm">
                🔐 Zwiększ bezpieczeństwo swojego konta — włącz uwierzytelnianie dwuskładnikowe (2FA).
            </p>
            <p class="text-xs mt-1">
                Wystarczy kilka sekund. Kod jednorazowy będzie wymagany tylko przy logowaniu.
            </p>
        </div>
        <a href="{{ route('2fa.setup') }}" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            Włącz 2FA
        </a>
    </div>
</div>
@endif
