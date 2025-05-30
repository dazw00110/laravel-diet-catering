<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CateringCalendar;

class CateringCalendarController extends Controller
{
    public function index()
    {
        $calendar = CateringCalendar::whereHas('order', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('calendar.index', compact('calendar'));
    }
}