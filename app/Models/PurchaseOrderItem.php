<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order_id',
        'sparepart_id',
        'quantity',
        'purchase_price',
        'expired_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2', // Memastikan purchase_price di-cast sebagai desimal dengan 2 angka di belakang koma
        'expired_date' => 'date',        // Mengubah ke tipe data date
    ];

    /**
     * Get the purchase order that owns the PurchaseOrderItem.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the sparepart that owns the PurchaseOrderItem.
     */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}