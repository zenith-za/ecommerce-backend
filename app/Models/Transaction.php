<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'total_amount', 'status', 'cart_items',
    ];

    protected $casts = [
        'cart_items' => 'array', // Store cart items as JSON
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}