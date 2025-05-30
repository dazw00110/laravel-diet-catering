<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('items')->get();
        return view('orders.index', compact('orders'));
    }

    public function store()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Koszyk jest pusty.');
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => $cartItems->sum(fn($item) => $item->product->price * $item->quantity),
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'status' => 'nowe',
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('orders.index')->with('success', 'Zamówienie zostało złożone.');
    }

    public function manage()
    {
        $orders = Order::with('user', 'items')->get();
        return view('orders.manage', compact('orders'));
    }
}