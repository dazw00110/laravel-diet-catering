<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(64);
        $email = $request->email;

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        info("Reset password token for {$email}: {$token}");

        return back()->with('status', 'Token resetu hasła został wygenerowany i wypisany w logach.');
    }

    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Użytkownik nie istnieje. Konto mogło zostać usunięte.']);
        }


        if (!$record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ten link już został użyty lub jest nieprawidłowy.']);
        }

        // Sprawdzenie ważności tokenu – 30 minut
        if (now()->diffInMinutes($record->created_at) > 30) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Token wygasł. Proszę wygenerować nowy.']);
        }

        if (!Hash::check($request->token, $record->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ten link już został użyty lub jest nieprawidłowy.']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Hasło zostało zresetowane.');
    }
}
