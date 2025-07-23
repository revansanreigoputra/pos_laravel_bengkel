<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'item_type', 'item_id', 'price', 'quantity'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function service()
    {
        // Ensure this relationship correctly points to the Service model
        return $this->belongsTo(Service::class, 'item_id');
    }

    public function sparepart()
    {
        // Ensure this relationship correctly points to the Sparepart model
        return $this->belongsTo(Sparepart::class, 'item_id');
    }
}