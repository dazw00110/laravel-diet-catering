<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'value',
        'type',
        'expires_at',
    ];

    public function cancellations()
    {
        return $this->hasMany(Cancellation::class);
    }
}
