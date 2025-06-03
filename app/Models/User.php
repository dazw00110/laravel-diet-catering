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
    ];

    protected $hidden = ['password', 'remember_token'];

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

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
