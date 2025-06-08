<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display a read-only view of the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $layout = match ($user->user_type_id) {
            1 => 'layouts.admin',
            2 => 'layouts.client',
            3 => 'layouts.staff',
            default => 'layouts.app'
        };

        return view('profile.show', [
            'user' => $user,
            'layout' => $layout,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        if (!in_array($user->user_type_id, [1, 2, 3])) {
            abort(403, 'Unauthorized.');
        }

        $layout = match ($user->user_type_id) {
            1 => 'layouts.admin',
            2 => 'layouts.client',
            3 => 'layouts.staff',
            default => 'layouts.app'
        };

        return view('profile.edit', [
            'user' => $user,
            'layout' => $layout,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->user_type_id === 1) { // ADMIN
            $validated = $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[\pL\s\'\-]+$/u',
                'last_name' => 'required|string|max:255|regex:/^[\pL\s\'\-]+$/u',
                'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        'unique:users,email,' . $user->id,
                        'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
                'is_vegan' => 'nullable|boolean',
                'is_vegetarian' => 'nullable|boolean',
            ]);
        } elseif ($user->user_type_id === 2 || $user->user_type_id === 3) {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'is_vegan' => 'nullable|boolean',
                'is_vegetarian' => 'nullable|boolean',
            ]);
        } else {
            abort(403);
        }

        $user->fill($validated)->save();

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // IS ADMIN AND LAST ADMIN CHECK
        if ($user->user_type_id === 1) {
            $adminCount = \App\Models\User::where('user_type_id', 1)->count();

            if ($adminCount <= 1) {
                return back()->withErrors([
                    'userDeletion' => 'Nie można usunąć ostatniego administratora systemu.',
                ]);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('status', 'password-updated');
    }

}
