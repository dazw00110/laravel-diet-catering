<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $query->where('is_vegan', false)->where('is_vegetarian', false);
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

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'calories' => 'required|integer|min:0',
        'is_vegan' => 'boolean',
        'is_vegetarian' => 'boolean',
        'image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
        $validated['image_path'] = $request->file('image')->store('products', 'public');
    } else {
        $validated['image_path'] = 'products/default.png';
    }

    Product::create($validated);

    return redirect()->route('admin.products.index')->with('success', 'Produkt został dodany.');
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
        'image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
        if ($product->image_path && $product->image_path !== 'products/default.png' && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }
        $validated['image_path'] = $request->file('image')->store('products', 'public');
    } else {
        $validated['image_path'] = $product->image_path ?? 'products/default.png';
    }

    $product->update($validated);

    return redirect()->route('admin.products.index')->with('success', 'Produkt został zaktualizowany.');
}


    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function destroy(Product $product)
    {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produkt został usunięty.');
    }
}
