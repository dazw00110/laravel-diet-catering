<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    public function create(Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403, 'Brak dostępu do tego zamówienia.');
    }

    // Pobierz wszystkie ID produktów z zamówienia
    $productIds = $order->items->pluck('product_id')->unique()->toArray();

    // Pobierz ID produktów, które użytkownik już ocenił w tym zamówieniu
    $reviewedProductIds = ProductReview::where('order_id', $order->id)
        ->where('user_id', auth()->id())
        ->pluck('product_id')
        ->unique()
        ->toArray();

    // Jeśli użytkownik ocenił WSZYSTKIE produkty, blokujemy dostęp
    if (count($reviewedProductIds) === count($productIds)) {
        return redirect()->route('client.orders.index')
            ->with('info', 'Już wystawiłeś opinię dla tego zamówienia.');
    }

    // Produkty do oceny to te, które nie mają jeszcze opinii
    $unreviewedItems = $order->items->filter(function ($item) use ($reviewedProductIds) {
        return !in_array($item->product_id, $reviewedProductIds);
    });

    return view('client.reviews.create', compact('order', 'unreviewedItems'));
}




    public function store(Request $request, Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    $data = $request->input('reviews', []);

    $validReviews = collect($data)->filter(fn($review) => isset($review['rating']) && $review['rating'] !== null);

    if ($validReviews->isEmpty()) {
        return redirect()->back()->withErrors(['reviews' => 'Musisz wystawić ocenę dla wszystkich produktów.']);
    }

    $productIds = collect($order->items->pluck('product_id'))
        ->map(fn($id) => (int) $id)
        ->sort()
        ->values()
        ->toArray();

    $reviewedProductIds = $validReviews
        ->pluck('product_id')
        ->map(fn($id) => (int) $id)
        ->sort()
        ->values()
        ->toArray();

    if ($productIds !== $reviewedProductIds) {
        return redirect()->back()->withErrors(['reviews' => 'Musisz wystawić ocenę dla wszystkich produktów.']);
    }

    foreach ($validReviews as $review) {
        ProductReview::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'product_id' => $review['product_id'],
            'rating' => $review['rating'],
            'comment' => $review['comment'] ?? null,
        ]);
    }

    return redirect()->route('client.orders.index')->with('success', 'Dziękujemy za opinię!');
}


}
