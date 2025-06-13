<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Discount;
use App\Models\DiscountUser;

class CartController extends Controller
{
    public function index()
    {
        $cart = Order::getOrCreateCartForUser(Auth::id())->load('items.product');

        $userDiscounts = DiscountUser::with('discount')
            ->where('user_id', Auth::id())
            ->whereHas('discount', function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get()
            ->pluck('discount');

        $days = max($cart->start_date->diffInDays($cart->end_date) + 1, 7);

    $loyaltyCodes = auth()->user()->discountCodes()
        ->where(function ($q) {
            $q->where('permanent', true)
            ->orWhere(function ($q) {
                $q->where('expires_at', '>=', now())->where('used', false);
            });
        })
        ->get();

    return view('client.cart.index', compact('cart', 'userDiscounts', 'loyaltyCodes', 'days'));

    }


    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = Order::getOrCreateCartForUser(Auth::id());
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $request->input('quantity');

        if ($item->quantity > 10) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nie można dodać więcej niż 10 sztuk danego produktu.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Nie można dodać więcej niż 10 sztuk danego produktu.');
        }

        $item->unit_price = $product->getCurrentPrice();
        $item->save();

        $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $cart->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produkt dodano do koszyka.'
            ]);
        }

        return redirect()->back()->with('success', 'Produkt dodano do koszyka.');
    }

    public function remove(OrderItem $item)
    {
        $userId = Auth::id();

        if ($item->order->user_id !== $userId || $item->order->status !== 'unordered') {
            abort(403);
        }

        $order = $item->order;
        $item->delete();

        $order->total_price = $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $order->save();

        return redirect()->route('client.cart.index')->with('success', 'Produkt usunięto z koszyka.');
    }

    public function update(Request $request)
    {
        $cart = Order::getOrCreateCartForUser(Auth::id());

        // AJAX PATCH support (item_id, quantity)
        if ($request->has('item_id') && $request->has('quantity')) {
            $item = $cart->items()->find($request->input('item_id'));
            $qty = (int)$request->input('quantity');
            if ($item && $qty > 0 && $qty <= 10) {
                $item->quantity = $qty;
                $item->save();
            }
            // If quantity = 0, remove the product
            if ($item && $qty == 0) {
                $item->delete();
            }
            $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
            $cart->save();

            // JSON response for AJAX
            return response()->json(['success' => true]);
        }

        // Classic button support (increase/decrease)
        if ($request->has('increase')) {
            $item = $cart->items()->find($request->input('increase'));
            if ($item && $item->quantity < 10) {
                $item->quantity++;
                $item->save();
            }
        } elseif ($request->has('decrease')) {
            $item = $cart->items()->find($request->input('decrease'));
            if ($item && $item->quantity > 1) {
                $item->quantity--;
                $item->save();
            }
        }

        $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Zaktualizowano ilość produktów.');
    }

    public function clear()
    {
        $cart = Order::getOrCreateCartForUser(Auth::id());

        $cart->items()->delete();
        $cart->total_price = 0;
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Koszyk został opróżniony.');
    }

    public function store(Request $request)
    {
        $cart = Order::getOrCreateCartForUser(Auth::id())->load('items.product');

        $request->validate([
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            'discount_code' => 'nullable|string',
        ]);

        if ($cart->items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Koszyk jest pusty.');
        }

        $days = $cart->start_date->diffInDays($cart->end_date) + 1;
        if ($days < 1) {
            return redirect()->route('client.cart.index')->with('error', 'Nieprawidłowy zakres dat.');
        }

        $total = 0;
        $freeItemBonus = 0;
        $bulkDiscount = 0;
        $loyaltyDiscount = 0;
        $discountSavings = 0;

        $recentOrders = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->count();

        $itemsByProduct = [];
        foreach ($cart->items as $item) {
            $item->unit_price = $item->product->getCurrentPrice();
            $item->save();

            $total += $item->quantity * $item->unit_price * $days;
            $itemsByProduct[$item->product_id] = ($itemsByProduct[$item->product_id] ?? 0) + $item->quantity;
        }

        foreach ($itemsByProduct as $productId => $qty) {
            if ($qty >= 5) {
                $product = Product::find($productId);
                if ($product) {
                    $freeItemBonus += $product->price * $days;
                }
            }
        }

        $totalAfterFree = $total - $freeItemBonus;

        if ($totalAfterFree >= 3000) {
            $bulkDiscount = $totalAfterFree * 0.15;
        } elseif ($totalAfterFree >= 2000) {
            $bulkDiscount = $totalAfterFree * 0.10;
        }

        $totalAfterBulk = $totalAfterFree - $bulkDiscount;

        if ($recentOrders >= 3) {
            $loyaltyDiscount = $totalAfterBulk * 0.05;
        }

        $totalAfterLoyalty = $totalAfterBulk - $loyaltyDiscount;

        $discountCode = $request->input('discount_code');
        if ($discountCode) {
            $code = \App\Models\DiscountCode::whereRaw('LOWER(code) = ?', [strtolower($discountCode)])
                ->where('user_id', Auth::id())
                ->where(function ($q) {
                    $q->where('permanent', true)
                    ->orWhere(function ($q) {
                        $q->where('expires_at', '>=', now())->where('used', false);
                    });
                })
                ->first();

            if ($code) {
                $discountSavings = $code->is_percentage
                    ? $totalAfterLoyalty * ($code->value / 100)
                    : min($code->value, $totalAfterLoyalty);

                if (!$code->permanent) {
                    $code->update(['used' => true]);
                }

                $cart->discount_code = $code->code;
            } else {
                $discount = Discount::whereRaw('LOWER(code) = ?', [strtolower($discountCode)])
                    ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
                    ->first();

                if ($discount) {
                    if ($discount->type === 'percentage') {
                        $discountSavings = $totalAfterLoyalty * ($discount->value / 100);
                    } elseif ($discount->type === 'fixed') {
                        $discountSavings = min($discount->value, $totalAfterLoyalty);
                    }

                    $discount->users()->detach(Auth::id());
                    $discount->delete();

                    $cart->discount_code = $discountCode;
                }
            }
        }


        $cart->city = $request->input('city');
        $cart->postal_code = $request->input('postal_code');
        $cart->street = $request->input('street');
        $cart->apartment_number = $request->input('apartment_number');

        $cart->total_price = round($totalAfterLoyalty - $discountSavings, 2);
        $cart->status = 'in_progress';

        $cart->save();

        return redirect()->route('client.orders.index')
            ->with('success', 'Zamówienie zostało złożone.');
    }


    public function updateDates(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date|before_or_equal:' . now()->addYear()->format('Y-m-d'),
        ]);

        $cart = Order::getOrCreateCartForUser(Auth::id());

        $cart->start_date = now()->parse($request->input('start_date'));
        $cart->end_date = now()->parse($request->input('end_date'));
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Zaktualizowano daty cateringu.');
    }


    public function extend()
    {
        $cart = Order::getOrCreateCartForUser(Auth::id());

        $cart->end_date = $cart->end_date->copy()->addDays(7);
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Zamówienie zostało przedłużone o tydzień.');
    }

    public function repeatOrder(Order $order)
    {
        $userId = auth()->id();

        if ($order->user_id !== $userId || !in_array($order->status, ['completed', 'cancelled'])) {
            abort(403, 'Brak dostępu do tego zamówienia.');
        }

        $cart = Order::getOrCreateCartForUser($userId);

        // Add products
        foreach ($order->items as $item) {
            $cartItem = $cart->items()->firstOrNew([
                'product_id' => $item->product_id,
            ]);

            $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $item->quantity;
            $cartItem->unit_price = $item->product->price;
            $cartItem->save();
        }

        // Copy address details from previous order to cart
        $cart->city = $order->city;
        $cart->postal_code = $order->postal_code;
        $cart->street = $order->street;
        $cart->apartment_number = $order->apartment_number;

        $cart->total_price = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $cart->save();

        return redirect()->route('client.cart.index')->with('success', 'Produkty i dane lokalizacji z poprzedniego zamówienia dodano do koszyka.');
    }


}
