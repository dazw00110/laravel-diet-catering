<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'calories',
        'is_active',
        'is_vegan',
        'is_vegetarian',
        'promotion_price',
        'promotion_expires_at',
        'image_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_vegan' => 'boolean',
        'is_vegetarian' => 'boolean',
        'promotion_expires_at' => 'datetime',
        'price' => 'decimal:2',
        'promotion_price' => 'decimal:2',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function hasActivePromotion()
    {
        return $this->promotion_price &&
               $this->promotion_expires_at &&
               $this->promotion_expires_at->isFuture();
    }

    public function getCurrentPrice()
    {
        return $this->hasActivePromotion() ? $this->promotion_price : $this->price;
    }
}
