
@extends('layouts.staff')

@section('title', 'Lista klientów')

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

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

<div class="mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label class="block text-sm mb-1">Imię i nazwisko</label>
            <input type="text" name="name" value="{{ request('name') }}" class="input input-bordered w-full">
        </div>
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="text" name="email" value="{{ request('email') }}" class="input input-bordered w-full">
        </div>
        <div class="col-span-full flex items-center gap-4 mt-2">
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Filtruj</button>
            <a href="{{ route('staff.users.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="is_vegetarian" value="1" {{ request()->has('is_vegetarian') ? 'checked' : '' }}>
                <span class="text-sm">Tylko wegetarianie</span>
            </label>
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="is_vegan" value="1" {{ request()->has('is_vegan') ? 'checked' : '' }}>
                <span class="text-sm">Tylko weganie</span>
            </label>
        </div>
    </form>
</div>

<div class="mb-4 text-right">
    <a href="{{ route('staff.users.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Dodaj klienta</a>
</div>

<table class="min-w-full bg-white shadow rounded text-sm">
    <thead>
        <tr>
            <th class="p-2 border-b text-center">
                <a href="{{ generate_sort_url('id') }}" class="hover:underline">ID {{ sort_icon('id') }}</a>
            </th>
            <th class="p-2 border-b text-center">Imię i nazwisko</th>
            <th class="p-2 border-b text-center">Email</th>
            <th class="p-2 border-b text-center">Wegetariański</th>
            <th class="p-2 border-b text-center">Wegański</th>
            <th class="p-2 border-b text-center">
                <a href="{{ generate_sort_url('created_at') }}" class="hover:underline">Utworzono {{ sort_icon('created_at') }}</a>
            </th>
            <th class="p-2 border-b text-center">Akcje</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr>
                <td class="p-2 border-b text-center">{{ $user->id }}</td>
                <td class="p-2 border-b text-center">{{ $user->first_name }} {{ $user->last_name }}</td>
                <td class="p-2 border-b text-center">{{ $user->email }}</td>
                <td class="p-2 border-b text-center">
                    {!! $user->is_vegetarian ? '<span class="text-green-600 font-bold">✔</span>' : '<span class="text-gray-400">–</span>' !!}
                </td>
                <td class="p-2 border-b text-center">
                    {!! $user->is_vegan ? '<span class="text-green-600 font-bold">✔</span>' : '<span class="text-gray-400">–</span>' !!}
                </td>
                <td class="p-2 border-b text-center">{{ $user->created_at->format('Y-m-d') }}</td>
                <td class="p-2 border-b text-center">
                    <a href="{{ route('staff.users.edit', $user) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">Edytuj</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-4 text-center text-gray-500">Brak wyników.</td>
            </tr>
        @endforelse
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
