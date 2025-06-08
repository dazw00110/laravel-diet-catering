<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TOTPService;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function showSetup(TOTPService $totp)
    {
        $user = Auth::user();

        if (!$user->totp_secret) {
            $user->totp_secret = $totp->generateSecret();
            $user->save();
        }

        $qr = $totp->getProvisioningUri($user->totp_secret, $user->email);

        return view('auth.2fa_setup', compact('qr'));
    }

    public function showVerify()
    {
        $user = Auth::user();

        $otp = \OTPHP\TOTP::create($user->totp_secret);
        $otp->setPeriod(30);
        $otp->setDigits(6);
        $otp->setLabel($user->email);

        $token = $otp->now();
        $secret = $user->totp_secret;

        \Log::info("DEBUG: [2FA VERIFY PAGE] TOTP token for {$user->email}: {$token}");
        \Log::info("DEBUG: [2FA VERIFY PAGE] TOTP secret for {$user->email}: {$secret}");

        if (app()->isLocal()) {
            echo "\n[2FA VERIFY PAGE] TOTP DEBUG: token = {$token}, secret = {$secret}\n";
        }

        return view('auth.2fa_verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();

        $otp = \OTPHP\TOTP::create($user->totp_secret);
        $otp->setPeriod(30); 
        $otp->setDigits(6);  
        $otp->setLabel($user->email);

        $token = $otp->now();
        $secret = $user->totp_secret;

    if ($otp->verify($request->code, null, 1)) {
        session(['2fa_verified' => true]);
        return redirect()->intended('/dashboard');
    }

    \Log::warning("2FA failed for {$user->email} with code: {$request->code}");

        return back()->withErrors(['code' => 'Invalid TOTP code.']);
    }

}
