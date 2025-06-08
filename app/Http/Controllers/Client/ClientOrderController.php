<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientOrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = auth()->user();

        $activeOrders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->get();

        $completedOrders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->get();

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

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'end_date' => now(),
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

        $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Produkty dodano do koszyka.');
    }
}

