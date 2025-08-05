<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code_part',
        'description',
        'selling_price',
        'supplier_id',
        'category_id',
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
    // Relasi untuk mengecek apakah sparepart sudah pernah terjual
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }


    /**
     * Accessor untuk mendapatkan total stok yang tersedia dan belum kadaluarsa.
     */
    public function getAvailableStockAttribute(): int
    {
        // Mengambil total stok dari purchase_order_items yang belum kadaluarsa
        // dan yang kuantitasnya masih lebih dari 0
        $validItems = $this->purchaseOrderItems()
                             ->where('quantity', '>', 0)
                             ->where(function ($query) {
                                 $query->where('expired_date', '>=', Carbon::today())
                                       ->orWhereNull('expired_date');
                             })
                             ->sum('quantity');

        return (int) $validItems;
    }

    /**
     * Cek apakah ada diskon yang aktif untuk sparepart ini.
     *
     * @return bool
     */
    public function isDiscountActive(): bool
    {
        $now = Carbon::now();
        return $this->discount_percentage > 0 &&
               ($this->discount_start_date === null || $now->greaterThanOrEqualTo($this->discount_start_date)) &&
               ($this->discount_end_date === null || $now->lessThanOrEqualTo($this->discount_end_date));
    }

    /**
     * Accessor untuk mendapatkan harga jual akhir setelah diskon.
     *
     * @return float
     */
    public function getFinalSellingPriceAttribute(): float
    {
        if ($this->isDiscountActive()) {
            return $this->selling_price * (1 - ($this->discount_percentage / 100));
        }
        return (float) $this->selling_price;
    }
}