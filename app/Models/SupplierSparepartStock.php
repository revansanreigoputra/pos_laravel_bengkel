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
        'invoice_number',    // Baru
        'invoice_file_path', // Baru
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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Sinkronkan kuantitas sparepart saat entri stok baru dibuat
        static::created(function ($stock) {
            $stock->syncSparepartQuantity();
        });

        // Sinkronkan kuantitas sparepart saat entri stok diperbarui
        static::updated(function ($stock) {
            $stock->syncSparepartQuantity();
        });

        // Sinkronkan kuantitas sparepart saat entri stok dihapus
        static::deleted(function ($stock) {
            $stock->syncSparepartQuantity();
        });
    }

    /**
     * Calculates and updates the total quantity of the associated sparepart.
     * This method assumes that the 'sparepart' model has a 'stockBatches'
     * relationship that returns all related SupplierSparepartStock entries.
     *
     * @return void
     */
    protected function syncSparepartQuantity()
    {
        $sparepart = $this->sparepart;
        // Pastikan relasi 'stockBatches' ada di model Sparepart
        // dan mengembalikan semua stok terkait untuk perhitungan total kuantitas.
        if ($sparepart) {
            $sparepart->quantity = $sparepart->stockBatches()->sum('quantity');
            $sparepart->save();
        }
    }
}