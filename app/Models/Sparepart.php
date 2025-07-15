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
        'supplier_id',
        'purchase_price',
        'selling_price',
        'expired_date',
        'inventory_batches'
    ];

    // protected $casts = [
    //     'inventory_batches' => 'array',
    //     'expired_date' => 'date',
    // ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Add new inventory batch
    public function addBatch($quantity, $price, $expiry = null)
    {
        $batches = $this->inventory_batches ?? [];
        
        $batches[] = [
            'id' => uniqid(),
            'quantity' => $quantity,
            'remaining' => $quantity,
            'purchase_price' => $price,
            'expiry_date' => $expiry,
            'added_at' => now()->toDateTimeString()
        ];

        $this->update([
            'quantity' => $this->quantity + $quantity,
            'inventory_batches' => $batches
        ]);
    }

    // Consume inventory (FIFO with expiry priority)
    public function consume($amount)
    {
        if ($this->quantity < $amount) {
            throw new \Exception("Insufficient stock for {$this->name}");
        }

        $batches = $this->inventory_batches ?? [];
        $remaining = $amount;

        // Sort batches by expiry date (soonest first) then by addition date
        usort($batches, function ($a, $b) {
            $aExpiry = $a['expiry_date'] ?? null;
            $bExpiry = $b['expiry_date'] ?? null;

            if ($aExpiry && $bExpiry) {
                return strtotime($aExpiry) <=> strtotime($bExpiry);
            }
            if ($aExpiry) return -1;
            if ($bExpiry) return 1;
            
            return strtotime($a['added_at']) <=> strtotime($b['added_at']);
        });

        foreach ($batches as &$batch) {
            if ($remaining <= 0) break;

            // Skip expired batches
            if (isset($batch['expiry_date']) && now()->gt($batch['expiry_date'])) {
                continue;
            }

            $deduct = min($remaining, $batch['remaining']);
            $batch['remaining'] -= $deduct;
            $remaining -= $deduct;
        }

        // Remove empty batches
        $batches = array_filter($batches, function ($batch) {
            return $batch['remaining'] > 0;
        });

        $this->update([
            'quantity' => $this->quantity - $amount,
            'inventory_batches' => array_values($batches) // Reset array keys
        ]);
    }

    // Get current inventory value (FIFO)
    public function getInventoryValue()
    {
        $value = 0;
        foreach ($this->inventory_batches ?? [] as $batch) {
            $value += $batch['remaining'] * $batch['purchase_price'];
        }
        return $value;
    }
}