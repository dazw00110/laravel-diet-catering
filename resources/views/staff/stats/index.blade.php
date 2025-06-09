@extends('layouts.staff')

@section('title', 'Statystyki sprzedaży')

@push('styles')
<style>
@media print {
    .no-print, header, nav, aside {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
    }

    canvas {
        max-width: 100%;
        display: block;
        page-break-inside: avoid;
    }
}
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto bg-white shadow-md p-6 rounded space-y-6 print-section">
    <h1 class="text-2xl font-bold mb-4">📊 Statystyki sprzedaży (Pracownik)</h1>

    {{-- 🔍 FILTR --}}
    <div x-data="{ loading: false }" class="bg-gray-100 p-4 rounded">
        <form @change="loading = true; $event.target.form.submit()" method="GET">
            <div class="flex flex-col md:flex-row gap-4 items-center">
            <label>
                📅 Miesiąc:
                <input
                    type="month"
                    name="month"
                    value="{{ request('month', now()->format('Y-m')) }}"
                    min="2000-01"
                    max="{{ now()->format('Y-m') }}"
                    class="input input-bordered"
                />
            </label>

                <label>
                    📌 Status:
                    <select name="status" class="input input-bordered">
                        <option value="completed" @selected(request('status') === 'completed')>Zakończone</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>W trakcie</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Anulowane</option>
                    </select>
                </label>
                <div x-show="loading" class="text-sm text-gray-500 italic">⏳ Ładowanie danych...</div>
            </div>
        </form>
    </div>

    <h2 class="font-semibold text-xl mt-4">📅 {{ $monthName }} (status: {{ $selectedStatus }})</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>📦 Zamówień: <strong>{{ $ordersTotal }}</strong></div>
        <div>💰 Łączna wartość: <strong>{{ number_format($totalValue, 2, ',', ' ') }} zł</strong></div>
        <div>📈 Średnia wartość: <strong>{{ number_format($averageValue, 2, ',', ' ') }} zł</strong></div>
        <div>👥 Klientów: <strong>{{ $uniqueClients }}</strong></div>
    </div>

    <h2 class="font-semibold text-lg mt-6">📊 Porównanie z {{ $prevMonthName }}</h2>
    <ul class="list-disc list-inside">
        <li>Zamówień: {{ $ordersTotal }} (poprzednio: {{ $previousOrdersTotal }})</li>
        <li>Łączna kwota: {{ number_format($totalValue, 2, ',', ' ') }} zł (poprzednio: {{ number_format($previousTotalValue, 2, ',', ' ') }} zł)</li>
        <li>Średnia cena: {{ number_format($averageValue, 2, ',', ' ') }} zł (poprzednio: {{ number_format($previousAverageValue, 2, ',', ' ') }} zł)</li>
    </ul>

    {{-- 🏆 TOP 5 wg wydanej kwoty --}}
    <h2 class="font-semibold text-lg mt-6">🏅 Top 5 klientów wg wydanej kwoty</h2>
    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">#</th>
                <th class="border px-2 py-1 text-center">Imię i nazwisko</th>
                <th class="border px-2 py-1 text-center">Kwota</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topClientsBySpend as $i => $client)
            <tr class="@if($i === 0) bg-yellow-200 @elseif($i === 1) bg-gray-200 @elseif($i === 2) bg-orange-100 @endif">
                <td class="border px-2 py-1 text-center font-bold">{{ $i + 1 }}</td>
                <td class="border px-2 py-1 text-center text-blue-600 font-semibold">{{ $client->user->first_name }} {{ $client->user->last_name }}</td>
                <td class="border px-2 py-1 text-center">{{ number_format($client->total, 2, ',', ' ') }} zł</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 🛍️ TOP 5 wg liczby zamówień --}}
    <h2 class="font-semibold text-lg mt-6">🧾 Top 5 klientów wg liczby zamówień</h2>
    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">#</th>
                <th class="border px-2 py-1 text-center">Imię i nazwisko</th>
                <th class="border px-2 py-1 text-center">Liczba zamówień</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topClientsByOrders as $i => $client)
            <tr class="@if($i === 0) bg-yellow-200 @elseif($i === 1) bg-gray-200 @elseif($i === 2) bg-orange-100 @endif">
                <td class="border px-2 py-1 text-center font-bold">{{ $i + 1 }}</td>
                <td class="border px-2 py-1 text-center text-blue-600 font-semibold">{{ $client->user->first_name }} {{ $client->user->last_name }}</td>
                <td class="border px-2 py-1 text-center">{{ $client->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 🍽️ Najczęściej zamawiane produkty --}}
    <h2 class="font-semibold text-lg mt-6">🍽️ Najczęściej zamawiane produkty</h2>
    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">#</th>
                <th class="border px-2 py-1 text-center">Produkt</th>
                <th class="border px-2 py-1 text-center">Sztuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $i => $item)
            <tr class="@if($i === 0) bg-yellow-200 @elseif($i === 1) bg-gray-200 @elseif($i === 2) bg-orange-100 @endif">
                <td class="border px-2 py-1 text-center font-bold">{{ $i + 1 }}</td>
                <td class="border px-2 py-1 text-center text-blue-600 font-semibold">{{ $item->product->name }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->total_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 💎 Najdroższe zamówienia --}}
    <h2 class="font-semibold text-lg mt-6">💎 Najdroższe zamówienia</h2>
    <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-center">#</th>
                <th class="border px-2 py-1 text-center">Zamówienie</th>
                <th class="border px-2 py-1 text-center">Klient</th>
                <th class="border px-2 py-1 text-center">Kwota</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topOrders as $i => $order)
            <tr class="@if($i === 0) bg-yellow-200 @elseif($i === 1) bg-gray-200 @elseif($i === 2) bg-orange-100 @endif">
                <td class="border px-2 py-1 text-center font-bold">{{ $i + 1 }}</td>
                <td class="border px-2 py-1 text-center">#{{ $order->id }}</td>
                <td class="border px-2 py-1 text-center text-blue-600 font-semibold">{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
                <td class="border px-2 py-1 text-center">{{ number_format($order->total_price, 2, ',', ' ') }} zł</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 📊 Wykresy --}}
    <h2 class="font-semibold text-lg mt-6">📊 Wydatki – Top 5 klientów</h2>
    <canvas id="clientChart" height="100"></canvas>

    <h2 class="font-semibold text-lg mt-6">🥧 Najczęściej zamawiane produkty</h2>
    <div class="max-w-sm mx-auto">
        <canvas id="productChart" class="mx-auto" width="300" height="300" style="max-width: 300px; aspect-ratio: 1;"></canvas>
    </div>

    <h2 class="font-semibold text-lg mt-6">📅 Wydatki tygodniowe ({{ $monthName }})</h2>
    <div class="max-w-2xl mx-auto">
        <canvas id="weeklyChart" height="60"></canvas>
    </div>

    <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 no-print">
        🖨️ Drukuj tylko raport
    </button>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const clientCtx = document.getElementById('clientChart');
    const productCtx = document.getElementById('productChart');
    const weeklyCtx = document.getElementById('weeklyChart');

    if (clientCtx) {
        new Chart(clientCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartClientLabels) !!},
                datasets: [{
                    label: 'Wydatki (zł)',
                    data: {!! json_encode($chartClientValues) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    if (productCtx) {
        new Chart(productCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartProductLabels) !!},
                datasets: [{
                    label: 'Zamówienia',
                    data: {!! json_encode($chartProductValues) !!},
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
                }]
            },
            options: { responsive: true }
        });
    }

    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($weeklyLabels) !!},
                datasets: [{
                    label: 'Łączne wydatki (zł)',
                    data: {!! json_encode($weeklyValues) !!},
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush
