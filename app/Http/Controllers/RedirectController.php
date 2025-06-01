<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        switch ($user->user_type_id) {
            case 1:
                return redirect()->route('admin.dashboard'); // Admin
            case 2:
                return redirect()->route('client.dashboard'); // Klient
            case 3:
                return redirect()->route('staff.dashboard'); // Pracownik
            default:
                return redirect()->route('home'); // Domyślnie
        }
    }
}
