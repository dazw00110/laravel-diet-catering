<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Cancellation;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('client')) {
            $clientInput = strtolower($request->client);
            $query->whereHas('user', function ($q) use ($clientInput) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$clientInput}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$clientInput}%"]);
            });
        }

        if ($request->filled('min_price')) $query->where('total_price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('total_price', '<=', $request->max_price);
        if ($request->filled('start_from')) $query->where('start_date', '>=', $request->start_from);
        if ($request->filled('end_to')) $query->where('end_date', '<=', $request->end_to);
        if ($request->filled('status')) $query->where('status', $request->status);

        if ($request->filled('sort')) {
            $direction = $request->get('dir', 'asc');
            if ($request->sort === 'client') {
                $query->join('users', 'orders.user_id', '=', 'users.id')
                      ->orderBy('users.last_name', $direction)
                      ->select('orders.*');
            } else {
                $query->orderBy($request->sort, $direction);
            }
        } else {
            $query->latest();
        }

        $orders = $query->paginate($request->get('per_page', 10))->appends($request->query());
        return view('staff.orders.index', compact('orders'));
    }

    public function create()
    {
        $staffs = User::whereHas('userType', fn($q) => $q->where('name', 'client'))->get();
        $products = Product::where('is_active', true)->get();
        $allDiscounts = Discount::with('users')->get();

        return view('staff.orders.create', compact('staffs', 'products', 'allDiscounts'));

    }


    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('staff.orders.show', compact('order'));
    }

    public function edit(Order $order)
{
    $clients = User::whereHas('userType', fn($q) => $q->where('name', 'client'))->get();
    $products = Product::where('is_active', true)->get();

    $recentOrders = Order::where('user_id', $order->user_id)
        ->whereBetween('created_at', [now()->subDays(7), now()])
        ->count();

    $discount = Discount::withTrashed()
        ->where('code', $order->discount_code)
        ->first();

    $discounts = $order->user->discounts ?? [];

    return view('staff.orders.edit', compact(
        'order', 'clients', 'products', 'recentOrders', 'discount', 'discounts'
    ));
}


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:unordered,in_progress,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'street' => 'required|string',
            'apartment_number' => 'nullable|string',
            'items' => 'required|array|max:10',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
            'discount_code' => 'nullable|string',
        ]);

        $totalQty = collect($request->items)->sum('quantity');
        if ($totalQty > 100) {
            return back()->withErrors(['items' => 'Maksymalnie 100 jednostek cateringu łącznie.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // ✅ Zapis adresu do bazy
            $order = Order::create([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_price' => 0,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'apartment_number' => $request->apartment_number,
            ]);

            $totalPrice = 0;
            $days = now()->parse($request->start_date)->diffInDays(now()->parse($request->end_date)) + 1;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'] * $days;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
                $totalPrice += $subtotal;
            }

            $newDiscountCode = null;
            if ($request->filled('discount_code')) {
                $code = strtolower($request->discount_code);
                $discount = Discount::whereRaw('LOWER(code) = ?', [$code])
                    ->whereHas('users', fn($q) => $q->where('user_id', $request->user_id))
                    ->first();

                if ($discount) {
                    $value = $discount->value;
                    if ($discount->type === 'percentage') {
                        $totalPrice -= ($value / 100) * $totalPrice;
                    } else {
                        $totalPrice -= min($value, $totalPrice);
                    }

                    $newDiscountCode = strtoupper(Str::random(6));
                    Discount::create([
                        'code' => $newDiscountCode,
                        'value' => $discount->value,
                        'type' => $discount->type,
                        'expires_at' => $discount->expires_at,
                    ]);

                    $discount->users()->detach($request->user_id);
                    $discount->delete();
                }
            }

            if ($totalQty >= 4) {
                $totalPrice -= $product->price * $days;
            }

            if ($totalPrice >= 3000) $totalPrice *= 0.85;
            elseif ($totalPrice >= 2000) $totalPrice *= 0.90;

            $recentOrders = Order::where('user_id', $request->user_id)
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->count();

            if ($recentOrders >= 3) $totalPrice *= 0.95;

            $order->update([
                'total_price' => round($totalPrice, 2),
                'discount_code' => $newDiscountCode,
            ]);

            if ($order->status === 'cancelled') {
                Cancellation::updateOrCreate(
                    ['order_id' => $order->id],
                    ['reason' => 'Anulowano przez system']
                );
            }

            DB::commit();
            return redirect()->route('staff.orders.index')->with('success', 'Zamówienie dodane.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Błąd przy zapisie zamówienia.'])->withInput();
        }
    }

    public function update(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:unordered,in_progress,completed,cancelled',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'city' => 'required|string|max:255',
        'postal_code' => 'required|string|max:20',
        'street' => 'required|string|max:255',
        'apartment_number' => 'nullable|string|max:50',
        'items' => 'required|array|max:10',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1|max:10',
    ]);

    $totalQty = collect($request->items)->sum('quantity');
    if ($totalQty > 100) {
        return back()->withErrors(['items' => 'Maksymalnie 100 jednostek cateringu łącznie.'])->withInput();
    }

    DB::beginTransaction();
    try {
        $order->update([
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'street' => $request->street,
            'apartment_number' => $request->apartment_number,
        ]);

        $order->items()->delete();

        $days = now()->parse($request->start_date)->diffInDays(now()->parse($request->end_date)) + 1;

        $totalPrice = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['quantity'] * $days;
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ]);
            $totalPrice += $subtotal;
        }

        if ($order->discount_code) {
            $discount = Discount::withTrashed()
                ->where('code', $order->discount_code)
                ->whereHas('users', fn($q) => $q->where('user_id', $order->user_id))
                ->first();

            if ($discount) {
                if ($discount->type === 'percentage') {
                    $totalPrice *= (1 - $discount->value / 100);
                } elseif ($discount->type === 'fixed') {
                    $totalPrice -= $discount->value;
                }
            }
        }

        $productCounts = [];
        foreach ($request->items as $item) {
            $productCounts[$item['product_id']] = ($productCounts[$item['product_id']] ?? 0) + $item['quantity'];
        }
        foreach ($productCounts as $productId => $qty) {
            if ($qty >= 5) {
                $product = Product::find($productId);
                if ($product) {
                    $totalPrice -= $product->price * $days;
                }
            }
        }

        if ($totalPrice >= 3000) {
            $totalPrice *= 0.85;
        } elseif ($totalPrice >= 2000) {
            $totalPrice *= 0.90;
        }

        $recentOrders = Order::where('user_id', $order->user_id)
            ->where('id', '!=', $order->id)
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->count();

        if ($recentOrders >= 3) {
            $totalPrice *= 0.95;
        }

        $order->update([
            'total_price' => round($totalPrice, 2),
        ]);

        if ($order->status === 'cancelled') {
            Cancellation::updateOrCreate(
                ['order_id' => $order->id],
                ['reason' => 'Anulowano przez system']
            );
        }

        DB::commit();
        return redirect()->route('staff.orders.index')->with('success', 'Zamówienie zaktualizowane.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Błąd przy aktualizacji zamówienia.'])->withInput();
    }
}


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('staff.orders.index')->with('success', 'Zamówienie zostało usunięte.');
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'cancelled') {
            $order->update(['status' => 'cancelled']);
            Cancellation::updateOrCreate(
                ['order_id' => $order->id],
                ['reason' => 'Anulowano przez system']
            );
        }
        return redirect()->route('staff.orders.index')->with('success', 'Zamówienie anulowane.');
    }

    public function complete(Order $order)
    {
        if ($order->status !== 'in_progress') {
            return back()->with('error', 'Tylko zamówienia w trakcie mogą być oznaczone jako zakończone.');
        }

        $order->status = 'completed';
        $order->save();

        return back()->with('success', 'Zamówienie zostało oznaczone jako zakończone.');
    }

}
