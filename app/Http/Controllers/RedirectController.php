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
                return redirect()->route('admin.dashboard');
            case 2:
                return redirect()->route('client.dashboard');
            case 3:
                return redirect()->route('staff.dashboard');
            default:
                abort(403, 'Nieznana rola użytkownika.');
        }
    }
}
