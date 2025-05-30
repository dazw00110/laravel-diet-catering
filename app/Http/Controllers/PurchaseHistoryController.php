<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;

class PurchaseHistoryController extends Controller
{
    public function index()
    {
        $history = PurchaseHistory::where('user_id', Auth::id())->with('product')->get();
        return view('history.index', compact('history'));
    }
}
