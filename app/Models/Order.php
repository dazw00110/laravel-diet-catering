<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'status',
    'start_date',
    'end_date',
    'total_price',
    'discount_code',
    'city',
    'postal_code',
    'street',
    'apartment_number',
    ];


 protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancellation()
    {
        return $this->hasOne(Cancellation::class);
    }

    // get unordered orders
    public function scopeUnordered($query)
    {
        return $query->where('status', 'unordered');
    }

    // get or create a cart for a user
    public static function getOrCreateCartForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'status' => 'unordered'],
            [
                'total_price' => 0,
                'start_date' => now()->startOfDay(),
                'end_date' => now()->startOfDay()->addDays(6), // 7-dniowy okres
            ]
        );
    }

}
