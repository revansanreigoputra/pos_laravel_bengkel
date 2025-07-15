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
        return $this->belongsTo(Service::class, 'item_id');
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'item_id');
    }
}
