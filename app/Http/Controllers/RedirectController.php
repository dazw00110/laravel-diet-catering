<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        return match ($user->user_type_id) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('client.dashboard'),
            3 => redirect()->route('staff.dashboard'),
            default => abort(403),
        };
    }
}
