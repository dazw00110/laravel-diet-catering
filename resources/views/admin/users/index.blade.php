@extends('layouts.admin')

@section('title', 'Lista użytkowników')

@section('content')
<div class="mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label class="block text-sm mb-1">Imię i nazwisko</label>
            <input type="text" name="name" value="{{ request('name') }}" placeholder="np. Jan Kowalski" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="text" name="email" value="{{ request('email') }}" placeholder="np. jan@kowalski.pl" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Rola</label>
            <select name="user_type_id" class="input input-bordered w-full">
                <option value="">-- Wybierz rolę --</option>
                <option value="1" {{ request('user_type_id') == '1' ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ request('user_type_id') == '2' ? 'selected' : '' }}>Client</option>
                <option value="3" {{ request('user_type_id') == '3' ? 'selected' : '' }}>Staff</option>
            </select>
        </div>

        <div class="col-span-full flex items-center gap-4 mt-2">
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 ml-4">Filtruj</button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>    
        
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" id="is_vegetarian" name="is_vegetarian" value="1" {{ request()->has('is_vegetarian') ? 'checked' : '' }}>
                <span class="text-sm">Tylko wegetarianie</span>
            </label>

            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" id="is_vegan" name="is_vegan" value="1" {{ request()->has('is_vegan') ? 'checked' : '' }}>
                <span class="text-sm">Tylko weganie</span>
            </label>
        </div>


    </form>
</div>



<div class="mb-4 text-right">
    <a href="{{ route('admin.users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Dodaj użytkownika</a>
</div>

@php
    $sort = request('sort');
    $dir = request('dir') === 'desc' ? 'asc' : 'desc';

    function generate_sort_url($field) {
        $currentSort = request('sort');
        $currentDir = request('dir') === 'desc' ? 'desc' : 'asc';
        $nextDir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $nextDir]);
    }

    function sort_icon($field) {
        $currentSort = request('sort');
        $currentDir = request('dir') === 'desc' ? 'desc' : 'asc';
        if ($currentSort === $field) {
            return $currentDir === 'desc' ? '↓' : '↑';
        }
        return '';
    }
@endphp

<table class="min-w-full bg-white shadow rounded">
    <thead>
        <tr>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('id') }}" class="hover:underline">ID {{ sort_icon('id') }}</a></th>
            <th class="p-2 border-b text-center">Imię i nazwisko</th>
            <th class="p-2 border-b text-center">Email</th>
            <th class="p-2 border-b text-center">Rola</th>
            <th class="p-2 border-b text-center">Wegetariański</th>
            <th class="p-2 border-b text-center">Wegański</th>
            <th class="p-2 border-b text-center">Utworzono</th>
            <th class="p-2 border-b text-center">Akcje</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td class="p-2 border-b text-center">{{ $user->id }}</td>
                <td class="p-2 border-b text-center">{{ $user->first_name }} {{ $user->last_name }}</td>
                <td class="p-2 border-b text-center">{{ $user->email }}</td>
                <td class="p-2 border-b text-center">
                    @if($user->user_type_id == 1) Admin
                    @elseif($user->user_type_id == 2) Client
                    @elseif($user->user_type_id == 3) Staff
                    @endif
                </td>
                <td class="p-2 border-b text-center">
                    @if ($user->is_vegetarian)
                        <span class="text-green-700 font-semibold">✔</span>
                    @else
                        <span class="text-gray-400">–</span>
                    @endif
                </td>
                <td class="p-2 border-b text-center">
                    @if ($user->is_vegan)
                        <span class="text-green-700 font-semibold">✔</span>
                    @else
                        <span class="text-gray-400">–</span>
                    @endif
                </td>
                <td class="p-2 border-b text-center">{{ $user->created_at->format('Y-m-d') }}</td>
                <td class="p-2 border-b text-center space-y-1 space-x-1">
                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">Edytuj</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Na pewno?')" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">Usuń</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-6">
    {{ $users->withQueryString()->links() }}
</div>

<div class="mt-4 flex justify-center items-center gap-4 text-sm">
    <span class="text-gray-600">Pokaż na stronę:</span>
    @foreach ([10, 30, 50] as $size)
        <a href="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}"
           class="px-3 py-1 rounded border {{ request('per_page', 10) == $size ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            {{ $size }}
        </a>
    @endforeach
</div>






@endsection
