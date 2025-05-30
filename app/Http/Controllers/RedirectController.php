<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        switch ($user->user_type_id) {
            case 1: return redirect('/admin');
            case 2: return redirect('/client');
            case 3: return redirect('/staff');
            default: abort(403);
        }
    }
}