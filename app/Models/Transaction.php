<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vehicle_number',
        'transaction_date',
        'total_price',
        'discount_amount',
        'invoice_number',
        'vehicle_model',
        'payment_method',
        'proof_of_transfer_url',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}