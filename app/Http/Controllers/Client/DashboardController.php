<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with(['reviews.user'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        $catering = Auth::user()->orders()
            ->where('status', 'in_progress')
            ->whereDate('end_date', '>', now())
            ->orderBy('end_date')
            ->first();

        $showReminder = $catering && $catering->end_date->diffInDays(now()) < 3 && !session()->get('hideReminder', false);

        return view('client.dashboard', compact('products', 'catering', 'showReminder'));
    }
}
