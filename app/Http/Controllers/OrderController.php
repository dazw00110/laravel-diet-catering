<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'discount_code' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create($validated);

            // Obsługa przypisanego kodu rabatowego
            if ($request->filled('discount_code')) {
                $code = strtolower($request->discount_code);
                $discount = Discount::whereRaw('LOWER(code) = ?', [$code])
                    ->whereHas('users', fn($q) => $q->where('user_id', $request->user_id))
                    ->first();

                if ($discount) {
                    $discount->users()->detach($request->user_id);
                    $discount->delete();
                }
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Zamówienie dodane.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Błąd przy dodawaniu zamówienia.'])->withInput();
        }
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_price' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $order->update($validated);

            if ($order->discount_code) {
                $discount = Discount::where('code', $order->discount_code)
                    ->whereHas('users', fn($q) => $q->where('user_id', $order->user_id))
                    ->first();

                if ($discount) {
                    $discount->users()->detach($order->user_id);
                    $discount->delete();
                }
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Zamówienie zaktualizowane.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Błąd przy aktualizacji zamówienia.'])->withInput();
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Zamówienie usunięte.');
    }
}
