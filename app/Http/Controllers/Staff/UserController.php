<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('user_type_id', 2);

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'ILIKE', '%' . $request->name . '%')
                ->orWhere('last_name', 'ILIKE', '%' . $request->name . '%');
            });
        }

        if ($request->filled('email')) {
            $query->where('email', 'ILIKE', '%' . $request->email . '%');
        }

        if ($request->has('is_vegetarian')) {
            $query->where('is_vegetarian', true);
        }

        if ($request->has('is_vegan')) {
            $query->where('is_vegan', true);
        }

        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('dir', 'desc');
        $allowedSorts = ['id', 'email', 'created_at'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'id';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        $perPage = in_array((int)$request->get('per_page'), [10, 30, 50]) ? (int)$request->get('per_page') : 10;

        $users = $query->paginate($perPage);

        return view('staff.users.index', compact('users'));
    }


    public function create()
    {
        return view('staff.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+$/u'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+(-[A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+)?$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birth_date' => ['required', 'date'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = new User();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->birth_date = $validated['birth_date'];
        $user->user_type_id = 2; 
        $user->password = Hash::make($validated['password']);
        $user->is_vegetarian = $request->has('is_vegetarian');
        $user->is_vegan = $request->has('is_vegan');
        $user->save();

        return redirect()->route('staff.users.index')->with('success', 'Klient został pomyślnie dodany.');
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
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_vegetarian = $request->has('is_vegetarian');
        $user->is_vegan = $request->has('is_vegan');
        $user->save();

        return redirect()->route('staff.users.index')->with('success', 'Dane klienta zostały zaktualizowane.');
    }
}
