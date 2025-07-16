<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierSparepartStock extends Model
{
    protected $fillable = [
        'supplier_id',
        'sparepart_id',
        'quantity',
        'purchase_price',
        'received_date',
        'note',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    // auto-syncs the quantity column in the spareparts table
    protected static function booted()
    {
        static::created(function ($stock) {
            $stock->syncSparepartQuantity();
        });

        static::updated(function ($stock) {
            $stock->syncSparepartQuantity();
        });

        static::deleted(function ($stock) {
            $stock->syncSparepartQuantity();
        });
    }

    protected function syncSparepartQuantity()
    {
        $sparepart = $this->sparepart;
        $sparepart->quantity = $sparepart->stockBatches()->sum('quantity');
        $sparepart->save();
    }
}
