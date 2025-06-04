<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_calories')) {
            $query->where('calories', '>=', $request->min_calories);
        }

        if ($request->filled('max_calories')) {
            $query->where('calories', '<=', $request->max_calories);
        }

        if ($request->boolean('is_vegan')) {
            $query->where('is_vegan', true);
        }

        if ($request->boolean('is_vegetarian')) {
            $query->where('is_vegetarian', true);
        }

        if ($request->boolean('non_vegan_vegetarian')) {
            $query->where('is_vegan', false)
                ->where('is_vegetarian', false);
        }

        if ($request->filled('sort')) {
            $sort = $request->get('sort');
            $dir = $request->get('dir', 'asc');

            if (in_array($sort, ['name', 'price', 'calories', 'id'])) {
                $query->orderBy($sort, $dir);
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->appends($request->query());

        return view('staff.products.index', compact('products'));
    }

    public function edit(Product $product)
    {
        return view('staff.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'calories' => 'required|integer|min:0',
            'is_vegan' => 'boolean',
            'is_vegetarian' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('staff.products.index')->with('success', 'Produkt został zaktualizowany.');
    }

    public function promotion(Product $product)
    {
        return view('staff.products.promotion', compact('product'));
    }

    public function storePromotion(Request $request, Product $product)
    {
        $request->validate([
            'promotion_price' => 'required|numeric|min:0.01',
            'hours' => 'required|integer|min:1|max:48',
        ]);

        $hours = (int) $request->hours;

        $product->update([
            'promotion_price' => $request->promotion_price,
            'promotion_expires_at' => now()->addHours($hours),
        ]);

        return redirect()->route('staff.products.index')
            ->with('success', 'Super promocja last minute została ustawiona na najbliższe ' . $hours . ' godzin.');
    }

    public function removePromotion(Product $product)
    {
        $product->update([
            'promotion_price' => null,
            'promotion_expires_at' => null,
        ]);

        return redirect()->route('staff.products.index')
            ->with('success', 'Promocja została usunięta.');
    }
}
