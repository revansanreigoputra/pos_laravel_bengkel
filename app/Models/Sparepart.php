<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'spareparts';

    protected $fillable = [
        'name',
        'code_part',
        'quantity',
        'purchase_price',
        'selling_price',
        'expired_date',
        'inventory_batches'
    ];

    // Many-to-Many relationship with suppliers via pivot table
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_sparepart_stocks')
            ->withPivot(['quantity', 'purchase_price', 'received_date', 'note'])
            ->withTimestamps();
    }

    // One-to-many: Sparepart can have many stock batches from different suppliers
    public function stockBatches()
    {
        return $this->hasMany(SupplierSparepartStock::class);
    }
}
