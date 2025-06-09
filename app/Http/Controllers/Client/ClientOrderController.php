<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Cancellation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientOrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = auth()->user();

        $activeOrders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->get();

        $completedOrders = Order::with(['items.product', 'cancellation'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->get();

        foreach ($activeOrders as $order) {
            foreach ($order->items as $item) {
                $item->has_review = ProductReview::where('user_id', $user->id)
                    ->where('product_id', $item->product_id)
                    ->exists();
            }
        }

        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                $item->has_review = ProductReview::where('user_id', $user->id)
                    ->where('product_id', $item->product_id)
                    ->exists();
            }
        }

        return view('client.orders.index', compact('activeOrders', 'completedOrders'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Brak dostępu do tego zamówienia.');
        }

        if ($order->status !== 'in_progress') {
            return back()->with('error', 'Tylko zamówienia w realizacji można przerwać.');
        }

        $order->update(['status' => 'cancelled']);

        Cancellation::create([
            'order_id' => $order->id,
            'reason' => 'Anulowane przez klienta',
            'discount_id' => null,
            'cancellation_date' => now(),
        ]);

        return back()->with('success', 'Zamówienie zostało przerwane.');
    }

    public function repeat(Order $order)
    {
        $userId = auth()->id();

        if ($order->user_id !== $userId) {
            abort(403, 'Brak dostępu do tego zamówienia.');
        }

        $cart = Order::getOrCreateCartForUser($userId);

        foreach ($order->items as $item) {
            $cartItem = $cart->items()->firstOrNew([
                'product_id' => $item->product_id,
            ]);

            $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $item->quantity;
            $cartItem->unit_price = $item->unit_price;
            $cartItem->save();
        }

        // Kopiuj daty i adres z poprzedniego zamówienia
        $cart->start_date = now()->toDateString();
        $cart->end_date = now()->copy()->addDays(
            $order->end_date->diffInDays($order->start_date)
        )->toDateString();

        $cart->city = $order->city;
        $cart->postal_code = $order->postal_code;
        $cart->street = $order->street;
        $cart->apartment_number = $order->apartment_number;

        $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Produkty dodano do koszyka.');
    }
}
