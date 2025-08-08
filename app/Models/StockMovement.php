<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'sparepart_id',
        'date',
        'type',
        'quantity',
        'remaining_stock',
    ];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
