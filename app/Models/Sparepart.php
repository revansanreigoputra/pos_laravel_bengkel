<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon; // Import Carbon untuk manipulasi tanggal

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price', // Harga beli dari supplier, mungkin bisa diubah menjadi purchase_price
        'selling_price', // Harga jual standar
        'stock', // Tambahkan 'stock' ke fillable jika Anda mengelola stok di sini
        'supplier_id',
        'category_id', // Tambahkan category_id ke fillable
        'discount_percentage', // Persentase diskon
        'discount_start_date', // Tanggal mulai diskon
        'discount_end_date',   // Tanggal berakhir diskon
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

    // Tambahkan relasi ke model Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Accessor untuk mendapatkan total stok yang tersedia dan belum kadaluarsa.
     * Ini adalah logika yang Anda berikan sebelumnya.
     */
    public function getAvailableStockAttribute()
    {
        // Mengambil total stok dari purchase_order_items yang belum kadaluarsa
        // dan yang kuantitasnya masih lebih dari 0
        $validItems = $this->purchaseOrderItems()
                           ->where('quantity', '>', 0) // Pastikan hanya item dengan kuantitas > 0
                           ->where(function ($query) {
                               $query->where('expired_date', '>=', Carbon::today())
                                     ->orWhereNull('expired_date');
                           })
                           ->sum('quantity');

        return $validItems;
    }

    /**
     * Cek apakah ada diskon yang aktif untuk sparepart ini.
     * Diskon dianggap aktif jika tanggal saat ini berada di antara start_date dan end_date.
     *
     * @return bool
     */
    public function isDiscountActive(): bool
    {
        $now = Carbon::now();
        // Diskon aktif jika ada discount_percentage > 0, dan tanggal saat ini
        // berada di antara discount_start_date dan discount_end_date.
        // Jika discount_start_date atau discount_end_date null, anggap diskon tidak terbatas waktu.
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
            // Hitung harga setelah diskon
            return $this->selling_price * (1 - ($this->discount_percentage / 100));
        }
        // Jika tidak ada diskon aktif, kembalikan harga jual standar
        return $this->selling_price;
    }
}
