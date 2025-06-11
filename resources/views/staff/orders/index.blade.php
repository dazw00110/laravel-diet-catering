@extends('layouts.staff')

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
            <a href="{{ route('staff.orders.index') }}" class="bg-gray-300 text-black rounded px-4 py-2">Resetuj</a>
        </div>
    </form>
</div>

<div class="mb-4 text-right">
    <a href="{{ route('staff.orders.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Dodaj zamówienie</a>
</div>

<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Klient</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Koniec</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kwota</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akcje</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @foreach($orders as $order)
            <tr>
                <td class="px-4 py-2">{{ $order->id }}</td>
                <td class="px-4 py-2">{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
                <td class="px-4 py-2">{{ $statuses[$order->status] ?? $order->status }}</td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($order->start_date)->format('Y-m-d') }}</td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($order->end_date)->format('Y-m-d') }}</td>
                <td class="px-4 py-2">{{ number_format($order->total_price, 2, ',', ' ') }} zł</td>
                <td class="px-4 py-2">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('staff.orders.show', $order->id) }}" class="bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs hover:bg-gray-300">Szczegóły</a>
                        <a href="{{ route('staff.orders.edit', $order->id) }}" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Edytuj</a>
                        <form method="POST" action="{{ route('staff.orders.destroy', $order->id) }}" class="inline" onsubmit="return confirm('Na pewno usunąć zamówienie?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Usuń</button>
                        </form>
                        <form method="POST" action="{{ route('staff.orders.complete', $order->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">Zakończ</button>
                        </form>
                        <form method="POST" action="{{ route('staff.orders.cancel', $order->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600">Przerwij</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
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
