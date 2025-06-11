<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('user_type_id', 2);

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'ILIKE', "%{$request->name}%")
                  ->orWhere('last_name', 'ILIKE', "%{$request->name}%");
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'ILIKE', "%{$request->email}%");
        }

        if ($request->has('is_vegetarian')) {
            $query->where('is_vegetarian', true);
        }

        if ($request->has('is_vegan')) {
            $query->where('is_vegan', true);
        }

        $allowedSorts = ['id', 'created_at'];
        $sort = in_array($request->sort, $allowedSorts) ? $request->sort : 'id';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $users = $query->orderBy($sort, $dir)
            ->paginate(in_array((int) $request->per_page, [10, 30, 50]) ? $request->per_page : 10)
            ->withQueryString();

        return view('staff.users.index', compact('users'));
    }

    public function create()
    {
        return view('staff.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\pL\s'.-]+$/u"],
            'last_name' => ['required', 'string', 'max:255', "regex:/^[\pL\s'.-]+$/u"],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'birth_date' => [
            'required',
            'date',
            'before_or_equal:' . now()->subYears(14)->toDateString(),
            'after_or_equal:' . now()->subYears(150)->toDateString(),
        ],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'password' => Hash::make($validated['password']),
            'user_type_id' => 2,
            'is_vegetarian' => $request->has('is_vegetarian'),
            'is_vegan' => $request->has('is_vegan'),
        ]);

        return redirect()->route('staff.users.index')->with('success', 'Klient został dodany.');
    }

    public function edit(User $user)
    {
        if ($user->user_type_id !== 2) {
            abort(403);
        }

        return view('staff.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->user_type_id !== 2) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', "regex:/^[\pL\s'.-]+$/u"],
            'last_name' => ['required', 'string', 'max:255', "regex:/^[\pL\s'.-]+$/u"],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->is_vegetarian = $request->has('is_vegetarian');
        $user->is_vegan = $request->has('is_vegan');

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('staff.users.index')->with('success', 'Dane klienta zostały zaktualizowane.');
    }
}
