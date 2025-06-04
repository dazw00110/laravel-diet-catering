<?php

namespace App\Http\Controllers\Staff;

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
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->name) . '%']);
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
            } else {
                $query->orderByDesc('id');
            }
        } else {
            $query->orderByDesc('id');
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
        'price' => 'required|numeric',
        'calories' => 'nullable|integer',
        'is_vegan' => 'nullable|boolean',
        'is_vegetarian' => 'nullable|boolean',
        'description' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('image')) {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $path = $request->file('image')->store('products', 'public');
        $validated['image_path'] = $path;
    }

    $product->update($validated);

    return redirect()->route('staff.products.index')->with('success', 'Produkt zaktualizowany.');
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

        $product->updateQuietly([
            'promotion_price' => $request->promotion_price,
            'promotion_expires_at' => now()->addHours($hours),
        ]);

        $redirectUrl = $this->buildRedirectUrlFromReferer($request);

        return redirect($redirectUrl)
            ->with('success', 'Super promocja last minute została ustawiona na najbliższe ' . $hours . ' godzin.');
    }

    public function removePromotion(Request $request, Product $product)
    {
        $product->updateQuietly([
            'promotion_price' => null,
            'promotion_expires_at' => null,
        ]);

        $redirectUrl = $this->buildRedirectUrlFromReferer($request);

        return redirect($redirectUrl)
            ->with('success', 'Promocja została usunięta.');
    }

    private function buildRedirectUrlFromReferer(Request $request)
    {
        $referer = $request->headers->get('referer');

        if ($referer && str_contains($referer, route('staff.products.index'))) {
            $parsedUrl = parse_url($referer);
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                return route('staff.products.index') . '?' . http_build_query($queryParams);
            }
        }

        return $this->buildRedirectUrl($request);
    }

    private function buildRedirectUrl(Request $request)
    {
        $baseUrl = route('staff.products.index');
        $params = [];

        foreach (['name', 'min_price', 'max_price', 'min_calories', 'max_calories', 'sort', 'dir', 'page'] as $param) {
            if ($request->filled($param)) {
                $params[$param] = $request->$param;
            }
        }

        foreach (['is_vegan', 'is_vegetarian', 'non_vegan_vegetarian'] as $boolParam) {
            if ($request->boolean($boolParam)) {
                $params[$boolParam] = '1';
            }
        }

        return $params ? $baseUrl . '?' . http_build_query($params) : $baseUrl;
    }
}
