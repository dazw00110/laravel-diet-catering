<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function reviews()
    {
        return $this->hasMany(\App\Models\ProductReview::class, 'order_id');
    }

    public function productReviews()
    {
        return $this->hasManyThrough(
            ProductReview::class,
            OrderItem::class,
            'order_id',
            'product_id',
            'id',
            'product_id'
        );
    }

    public function scopeUnordered($query)
    {
        return $query->where('status', 'unordered');
    }

    public static function getOrCreateCartForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'status' => 'unordered'],
            [
                'total_price' => 0,
                'start_date' => now()->startOfDay(),
                'end_date' => now()->startOfDay()->addDays(6),
            ]
        );
    }

    public function hasReviews(): bool
    {
        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            $alreadyReviewed = ProductReview::where('user_id', $this->user_id)
                ->where('product_id', $product->id)
                ->exists();

            if (!$alreadyReviewed) return false;
        }
        return true;
    }





}
