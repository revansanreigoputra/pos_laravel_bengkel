<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    protected $fillable = [
        'customer_name',
        'vehicle_number',
        'transaction_date',
        'total_price',
        'discount_amount',
        'invoice_number',
        'vehicle_model', 
        'payment_method',
        'proof_of_transfer_url', 
        'status'         
    ];

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