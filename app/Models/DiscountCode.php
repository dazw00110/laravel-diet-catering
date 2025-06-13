<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'code', 'description',
        'value', 'is_percentage', 'permanent',
        'used', 'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
