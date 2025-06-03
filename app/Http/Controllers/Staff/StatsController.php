<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $selectedStatus = $request->input('status', 'completed');

        $month = Carbon::parse($selectedMonth);
        $prevMonth = $month->copy()->subMonth();

        // select month for orders
        $orders = Order::with('user')
            ->where('status', $selectedStatus)
            ->whereMonth('start_date', $month->month)
            ->whereYear('start_date', $month->year);

        $ordersTotal = $orders->count();
        $totalValue = $orders->sum('total_price');
        $averageValue = $ordersTotal > 0 ? $totalValue / $ordersTotal : 0;
        $uniqueClients = $orders->distinct('user_id')->count('user_id');

        // previous month orders
        $previousOrders = Order::where('status', $selectedStatus)
            ->whereMonth('start_date', $prevMonth->month)
            ->whereYear('start_date', $prevMonth->year);

        $previousOrdersTotal = $previousOrders->count();
        $previousTotalValue = $previousOrders->sum('total_price');
        $previousAverageValue = $previousOrdersTotal > 0 ? $previousTotalValue / $previousOrdersTotal : 0;

        // TOP 5 clients by spend
        $topClientsBySpend = Order::selectRaw('user_id, SUM(total_price) as total')
            ->where('status', $selectedStatus)
            ->whereMonth('start_date', $month->month)
            ->whereYear('start_date', $month->year)
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // TOP 5 clients by number of orders
        $topClientsByOrders = Order::selectRaw('user_id, COUNT(*) as count')
            ->where('status', $selectedStatus)
            ->whereMonth('start_date', $month->month)
            ->whereYear('start_date', $month->year)
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // TOP 5 orders by value
        $topOrders = Order::with('user')
            ->where('status', $selectedStatus)
            ->whereMonth('start_date', $month->month)
            ->whereYear('start_date', $month->year)
            ->orderByDesc('total_price')
            ->limit(5)
            ->get();

        // TOP 5 procucts by quantity sold
        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->whereHas('order', function ($query) use ($month, $selectedStatus) {
                $query->where('status', $selectedStatus)
                      ->whereMonth('start_date', $month->month)
                      ->whereYear('start_date', $month->year);
            })
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // data for chart clients
        $chartClientLabels = $topClientsBySpend->map(function ($c) {
            return $c->user->first_name . ' ' . $c->user->last_name;
        })->toArray();
        $chartClientValues = $topClientsBySpend->pluck('total')->map(fn($val) => round($val, 2))->toArray();

        // data for chart products
        $chartProductLabels = $topProducts->pluck('product.name')->toArray();
        $chartProductValues = $topProducts->pluck('total_quantity')->toArray();

        $monthName = $month->locale('pl')->isoFormat('MMMM YYYY');
        $prevMonthName = $prevMonth->locale('pl')->isoFormat('MMMM YYYY');

        // weekly spend
        $weeklySpend = Order::selectRaw("EXTRACT(WEEK FROM start_date) as week, SUM(total_price) as total")
            ->where('status', $selectedStatus)
            ->whereMonth('start_date', $month->month)
            ->whereYear('start_date', $month->year)
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $weeklyLabels = $weeklySpend->pluck('week')->map(fn($w) => "Tydzień " . intval($w));
        $weeklyValues = $weeklySpend->pluck('total');


        return view('staff.stats.index', compact(
            'selectedMonth', 'selectedStatus',
            'ordersTotal', 'totalValue', 'averageValue',
            'previousOrdersTotal', 'previousTotalValue', 'previousAverageValue',
            'monthName', 'prevMonthName', 'uniqueClients',
            'topClientsBySpend', 'topClientsByOrders',
            'topOrders', 'topProducts',
            'chartClientLabels', 'chartClientValues',
            'chartProductLabels', 'chartProductValues',
            'weeklyLabels', 'weeklyValues'

        ));
    }
}
