<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'birth_date',
        'user_type_id',
        'is_vegan',
        'is_vegetarian',
        'avatar_url',
        'totp_secret', // TOTP
    ];

    protected $hidden = ['password', 'remember_token', 'totp_secret']; // hide TOTP from JSON serialization

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_vegan' => 'boolean',
        'is_vegetarian' => 'boolean',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }
}
