<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $topProducts = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('stats.index', compact('totalOrders', 'topProducts'));
    }
}
