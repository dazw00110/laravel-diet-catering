<?php

namespace App\Http\Controllers;

use App\Models\Product; // jeśli potrzebujesz modeli
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
{
    $products = Product::all();

    return view('landing', compact('products'));
}

}
