<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        Order::create($validated);
        return redirect()->route('orders.index')->with('success', 'Zamówienie dodane.');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric',
        ]);

        $order->update($validated);
        return redirect()->route('orders.index')->with('success', 'Zamówienie zaktualizowane.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Zamówienie usunięte.');
    }
}
