<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class GuestOfferController extends Controller
{
    public function index(Request $request)
{
    $query = Product::withAvg('reviews', 'rating')->with('reviews.user');

    if ($search = $request->input('search')) {
        $query->where('name', 'like', "%{$search}%");
    }

    if ($priceMin = $request->input('price_min')) {
        $query->where('price', '>=', $priceMin);
    }

    if ($priceMax = $request->input('price_max')) {
        $query->where('price', '<=', $priceMax);
    }

    switch ($request->input('sort')) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'rating_desc':
            $query->orderBy('reviews_avg_rating', 'desc');
            break;
    }

    $perPage = $request->input('per_page', 12);

    $products = $query->paginate($perPage);

    return view('guest.offers', compact('products'));
}

}
