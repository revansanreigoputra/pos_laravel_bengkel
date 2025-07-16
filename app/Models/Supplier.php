<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'nama_barang',
        'tipe_barang',
        'jumlah',
        'harga',
        'tanggal_masuk',
    ];

    // Many-to-Many relationship with Spareparts via pivot table
    public function spareparts()
    {
        return $this->belongsToMany(Sparepart::class, 'supplier_sparepart_stocks')
            ->withPivot(['quantity', 'purchase_price', 'received_date', 'note'])
            ->withTimestamps();
    }

    // One-to-many: A supplier may deliver multiple stock batches
    public function stockDeliveries()
    {
        return $this->hasMany(SupplierSparepartStock::class);
    }
}
