<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CateringCalendar extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'active_day'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
