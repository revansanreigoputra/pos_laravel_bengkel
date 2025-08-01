<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Pastikan Carbon diimpor jika menggunakan method isDiscountActive()

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code_part',
        'selling_price',
        'category_id',
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
    ];

    protected $casts = [
        'discount_start_date' => 'date',
        'discount_end_date' => 'date',
    ];

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke PurchaseOrderItem (PENTING untuk menghitung stok dan harga beli)
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Accessor untuk menghitung harga jual akhir setelah diskon
    public function getFinalSellingPriceAttribute()
    {
        if ($this->isDiscountActive()) {
            return $this->selling_price * (1 - ($this->discount_percentage / 100));
        }
        return $this->selling_price;
    }

    // Method untuk memeriksa apakah diskon aktif
    public function isDiscountActive()
    {
        $now = Carbon::now();
        return $this->discount_percentage > 0 &&
               $this->discount_start_date && $this->discount_start_date->lte($now) &&
               $this->discount_end_date && $this->discount_end_date->gte($now);
    }
}