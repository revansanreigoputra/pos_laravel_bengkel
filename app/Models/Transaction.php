<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    // Add 'discount_amount' to the fillable properties
    protected $fillable = ['customer_name', 'vehicle_number', 'transaction_date', 'total_price', 'discount_amount'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}