
@extends('layouts.admin')

@section('title', 'Lista zamówień')

@section('content')
<div class="mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label class="block text-sm mb-1">Klient</label>
            <input type="text" name="client" value="{{ request('client') }}" placeholder="Klient" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Cena od</label>
            <input type="number" min="0" step="0.01" name="min_price" value="{{ request('min_price') }}" placeholder="Cena od" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Cena do</label>
            <input type="number" min="0" step="0.01" name="max_price" value="{{ request('max_price') }}" placeholder="Cena do" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Data początkowa (od min)</label>
            <input type="date" name="start_from" value="{{ request('start_from') }}" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Data końcowa (do max)</label>
            <input type="date" name="end_to" value="{{ request('end_to') }}" class="input input-bordered w-full">
        </div>

        <div>
            <label class="block text-sm mb-1">Status</label>
            <select name="status" class="input input-bordered w-full">
                <option value="">-- Status --</option>
                <option value="unordered" {{ request('status') === 'unordered' ? 'selected' : '' }}>W koszyku</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>W trakcie</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Zakończono</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Anulowano</option>
            </select>
        </div>

        <div class="col-span-full flex gap-2 mt-2">
            <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Filtruj</button>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>
        </div>
    </form>
</div>

<div class="mb-4 text-right">
    <a href="{{ route('admin.orders.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Dodaj zamówienie</a>
</div>

<table class="min-w-full bg-white shadow rounded">
    <thead>
        <tr>
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
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('id') }}" class="hover:underline">ID {{ sort_icon('id') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('client') }}" class="hover:underline">Klient {{ sort_icon('client') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('total_price') }}" class="hover:underline">Cena {{ sort_icon('total_price') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('status') }}" class="hover:underline">Status {{ sort_icon('status') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('start_date') }}" class="hover:underline">Start {{ sort_icon('start_date') }}</a></th>
            <th class="p-2 border-b text-center"><a href="{{ generate_sort_url('end_date') }}" class="hover:underline">Koniec {{ sort_icon('end_date') }}</a></th>
            <th class="p-2 border-b text-center">Akcje</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            @php
                $labels = [
                    'unordered' => ['W koszyku', 'bg-blue-100 text-blue-800'],
                    'in_progress' => ['W trakcie', 'bg-yellow-100 text-yellow-800'],
                    'completed' => ['Zakończono', 'bg-green-100 text-green-800'],
                    'cancelled' => ['Anulowano', 'bg-red-100 text-red-800'],
                ];
                [$label, $class] = $labels[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-800'];
            @endphp
            <tr>
                <td class="p-2 border-b text-center">{{ $order->id }}</td>
                <td class="p-2 border-b text-center">{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
                <td class="p-2 border-b text-center">{{ number_format($order->total_price, 2) }} zł</td>
                <td class="p-2 border-b text-center">
                    <span class="px-2 py-1 rounded text-sm font-medium {{ $class }}">{{ $label }}</span>
                </td>
                <td class="p-2 border-b text-center">{{ $order->start_date }}</td>
                <td class="p-2 border-b text-center">{{ $order->end_date }}</td>
                <td class="p-2 border-b text-center space-y-1 space-x-1">
                    <a href="{{ route('admin.orders.show', $order) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-sm hover:bg-blue-600">Pokaż</a>
                    <a href="{{ route('admin.orders.edit', $order) }}" class="bg-yellow-400 text-white px-2 py-1 rounded text-sm hover:bg-yellow-500">Edytuj</a>
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline">
    @csrf @method('DELETE')
    <button onclick="return confirm('Na pewno?')" class="bg-red-500 text-white px-2 py-1 rounded text-sm hover:bg-red-600">Usuń</button>
</form>

@php
    $canCancel = $order->status === 'in_progress';
@endphp

<form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="inline">
    @csrf
    <button
        @if ($order->status === 'in_progress')
            onclick="return confirm('Czy na pewno anulować to zamówienie?')"
            class="bg-gray-500 hover:bg-gray-600 text-white px-2 py-1 rounded text-sm"
        @else
            disabled
            title="Można anulować tylko zamówienia w trakcie"
            class="bg-gray-300 text-white px-2 py-1 rounded text-sm opacity-50 cursor-not-allowed"
        @endif
    >
        Anuluj
    </button>
</form>




                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="p-4 text-center text-gray-500">Brak wyników</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-6">
    {{ $orders->links() }}
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
