<?php

namespace App\Models;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon; // Ensure Carbon is imported if not using Laravel's default date casting

class Sparepart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code_part',
        'quantity',
        'supplier_id', // Make sure this is handled in your controller's create/update if it's always required
        'purchase_price',
        'selling_price',
        'expired_date',
        'inventory_batches', // This is for FIFO tracking
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
        'category_id', // Added for category relationship
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inventory_batches' => 'array',
        'expired_date' => 'date', 
        'discount_start_date' => 'date',
        'discount_end_date' => 'date',
    ];

    /**
     * Define the relationship with Supplier.
     * A Sparepart belongs to one Supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Define the relationship with SupplierSparepartStock (or whatever model holds the stock batches).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stockBatches()
    {
        // Assuming your stock batches are managed by a model like SupplierSparepartStock
        // and it has a foreign key `sparepart_id`
        return $this->hasMany(SupplierSparepartStock::class, 'sparepart_id');
        // You might need to adjust 'SupplierSparepartStock' to your actual stock model name
    }


    /**
     * Accessor to check if the discount is currently active.
     *
     * @return bool
     */
    public function isDiscountActive(): bool
    {
        if ($this->discount_percentage <= 0) {
            return false; // No discount percentage means no active discount
        }

        $now = now(); // Gets the current Carbon instance

        // Check if the discount has started (or if start date is null, meaning it's always started)
        $isStarted = is_null($this->discount_start_date) || $now->gte($this->discount_start_date);
        // Check if the discount has not expired (or if end date is null, meaning it never expires)
        $isNotExpired = is_null($this->discount_end_date) || $now->lte($this->discount_end_date);

        return $isStarted && $isNotExpired;
    }

    /**
     * Accessor to get the final selling price after applying the discount.
     * You can call this in your view with $sparepart->final_selling_price.
     *
     * @return float
     */
    public function getFinalSellingPriceAttribute(): float
    {
        if ($this->isDiscountActive()) {
            $discountAmount = $this->selling_price * ($this->discount_percentage / 100);
            return (float) ($this->selling_price - $discountAmount);
        }

        return (float) $this->selling_price;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}