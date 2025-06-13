<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['reviews.user'])
            ->withAvg('reviews', 'rating');

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int)$request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int)$request->input('price_max'));
        }

        if ($request->input('diet') === 'vegan') {
            $query->where('is_vegan', true);
        } elseif ($request->input('diet') === 'vegetarian') {
            $query->where('is_vegetarian', true);
        }

        if ($request->filled('sort')) {
            switch ($request->input('sort')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating_asc':
                    $query->orderBy('reviews_avg_rating', 'asc');
                    break;
                case 'rating_desc':
                    $query->orderBy('reviews_avg_rating', 'desc');
                    break;
            }
        } else {
            $query->orderByDesc('reviews_avg_rating');
        }

        $perPage = in_array((int)$request->input('per_page'), [12, 24, 36])
            ? (int)$request->input('per_page')
            : 12;

        $products = $query->paginate($perPage)->withQueryString();

        $catering = Auth::user()->orders()
            ->where('status', 'in_progress') 
            ->whereDate('end_date', '>', now())
            ->orderBy('end_date')
            ->first();


        $showReminder = $catering && $catering->end_date->diffInDays(now()) < 3 && !session()->get('hide_reminder', false);

        return view('client.products.index', compact('products', 'catering', 'showReminder'));
    }
}
