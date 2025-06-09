<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Discount;

class ProductReviewController extends Controller
{
    /**
     * Pokaż formularz opinii tylko dla nieocenionych produktów.
     */
    public function create(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->items = $order->items->filter(function ($item) use ($order) {
            $product = $item->product;

            if (!$product) return false;

            return !ProductReview::where('user_id', $order->user_id)
                ->where('product_id', $product->id)
                ->exists();
        });

        if ($order->items->isEmpty()) {
            return redirect()
                ->route('client.orders.index')
                ->with('error', 'Wszystkie produkty z tego zamówienia zostały już ocenione.');
        }

        return view('client.reviews.create', compact('order'));
    }

    /**
     * Zapisz wystawione opinie i przypisz losowy kupon jeśli średnia < 2.
     */
    public function store(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string|max:1000',
        ]);

        $reviews = $request->input('reviews');

        foreach ($reviews as $reviewData) {
            ProductReview::create([
                'user_id' => auth()->id(),
                'product_id' => $reviewData['product_id'],
                'rating' => $reviewData['rating'],
                'comment' => $reviewData['comment'] ?? null,
            ]);
        }

        $productIdsInOrder = $order->items->pluck('product_id')->toArray();

        $ratingsForThisOrder = collect($reviews)
            ->filter(fn($r) => in_array($r['product_id'], $productIdsInOrder))
            ->pluck('rating');

        $average = $ratingsForThisOrder->avg();

        if (
            $order->status === 'cancelled' &&
            $order->cancellation &&
            !$order->cancellation->discount &&
            $average < 2.0
        ) {
            $discount = Discount::where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->whereDoesntHave('users', fn($q) => $q->where('user_id', auth()->id()))
                ->inRandomOrder()
                ->first();

            if ($discount) {
                $discount->users()->attach(auth()->id());

                $order->cancellation->update([
                    'discount_id' => $discount->id,
                ]);

                return redirect()->route('client.orders.index')
                    ->with('success', 'Otrzymałeś rabat za niezadowolenie z cateringu: ' . $discount->code);
            } else {
                return redirect()->route('client.orders.index')
                    ->with('error', 'Nie znaleziono dostępnych kuponów rabatowych.');
            }
        }

        return redirect()->route('client.orders.index')
            ->with('success', 'Dziękujemy za opinię!');
    }
}
