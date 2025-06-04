<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
{
    $query = User::with('userType');

    if ($request->filled('name')) {
        $query->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%' . strtolower($request->name) . '%']);
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->has('is_vegetarian')) {
        $query->where('is_vegetarian', true);
    }

    if ($request->has('is_vegan')) {
        $query->where('is_vegan', true);
    }


    if ($request->filled('user_type_id')) {
        $query->where('user_type_id', $request->user_type_id);
    }

    // Sorting logic
    $sortable = ['id', 'first_name', 'last_name', 'created_at'];
    $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'id';
    $direction = $request->get('dir') === 'asc' ? 'asc' : 'desc';

    if ($sort === 'name') {
        $query->orderBy('first_name', $direction)->orderBy('last_name', $direction);
    } else {
        $query->orderBy($sort, $direction);
    }

    $perPage = in_array($request->get('per_page'), [10, 30, 50]) ? $request->get('per_page') : 10;
    $users = $query->paginate($perPage)->appends($request->all());


    return view('admin.users.index', compact('users'));
}

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'required|date',
            'user_type_id' => ['required', Rule::in([1, 2, 3])],
        ]);

        $validated['is_vegetarian'] = $request->has('is_vegetarian');
        $validated['is_vegan'] = $request->has('is_vegan');

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'user_type_id' => ['required', Rule::in([1, 2, 3])],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $validated['is_vegetarian'] = $request->has('is_vegetarian');
        $validated['is_vegan'] = $request->has('is_vegan');


        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
