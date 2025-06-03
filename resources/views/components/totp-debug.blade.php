@if(app()->environment('local') && auth()->check() && auth()->user()->totp_secret)
    @php
        $totp = \OTPHP\TOTP::create(auth()->user()->totp_secret);
        $totp->setPeriod(30);
        $totp->setDigits(6);
        $totp->setLabel(auth()->user()->email);
        $code = $totp->now();
    @endphp

    <div class="fixed bottom-0 left-0 w-full bg-gray-800 text-white text-sm px-4 py-2 z-50 shadow-inner text-center">
        <span class="font-semibold">[TOTP DEBUG]</span>
        Aktualny kod 2FA: <span class="bg-gray-700 px-2 py-1 rounded text-green-400 tracking-widest">{{ $code }}</span>
        | Zmienny co 30s
    </div>
@endif
