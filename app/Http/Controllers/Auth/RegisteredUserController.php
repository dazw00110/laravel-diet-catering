<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-]+$/u'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'birth_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->subYears(150)->toDateString(),
                'before_or_equal:' . now()->subYears(14)->toDateString(),
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            ],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'password' => Hash::make($validated['password']),
            'user_type_id' => 2, // klient
            'avatar_url' => null,
            'is_vegan' => $request->boolean('is_vegan'),
            'is_vegetarian' => $request->boolean('is_vegetarian'),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('profile');
    }
}
