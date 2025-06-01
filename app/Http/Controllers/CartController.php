<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('cart.index')->with('success', 'Produkt dodany do koszyka.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:cart,id'
        ]);

        Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('cart.index')->with('success', 'Produkt usunięty z koszyka.');
    }
}
