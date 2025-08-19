<?php

namespace App\Services;

use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\PurchaseOrderItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;
use App\Services\NotificationService;

class TransactionService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Membuat transaksi baru + kurangi stok sparepart.
     */
    public function createTransaction(array $transactionData, array $itemsData): Transaction
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::create($transactionData);
            $totalPrice = 0;

            foreach ($itemsData as $item) {
                if ($item['item_type'] === 'sparepart') {
                    $sparepart = Sparepart::find($item['item_id']);
                    if (!$sparepart) {
                        throw new Exception("Sparepart dengan ID {$item['item_id']} tidak ditemukan.");
                    }

                    $requestedQuantity = $item['quantity'];

                    // Hitung stok tersedia dari batch
                    $availableStock = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                        ->selectRaw('SUM(quantity - sold_quantity) as available_stock')
                        ->where(function ($query) {
                            $query->where('expired_date', '>=', Carbon::today())
                                  ->orWhereNull('expired_date');
                        })
                        ->value('available_stock') ?? 0;

                    if ($availableStock < $requestedQuantity) {
                        throw new Exception("Stok untuk sparepart '{$sparepart->name}' tidak mencukupi. Sisa: {$availableStock}");
                    }

                    // FEFO/FIFO batch
                    $batches = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                        ->whereRaw('quantity - sold_quantity > 0')
                        ->where(function ($query) {
                            $query->where('expired_date', '>=', Carbon::today())
                                  ->orWhereNull('expired_date');
                        })
                        ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date ASC, created_at ASC')
                        ->get();

                    $remainingQuantity = $requestedQuantity;
                    foreach ($batches as $batch) {
                        if ($remainingQuantity <= 0) break;

                        $availableQty = $batch->quantity - $batch->sold_quantity;
                        if ($availableQty <= 0) continue;

                        $quantityToUse = min($remainingQuantity, $availableQty);

                        $transaction->items()->create([
                            'item_type' => 'sparepart',
                            'item_id' => $sparepart->id,
                            'purchase_order_item_id' => $batch->id,
                            'price' => $sparepart->final_selling_price,
                            'quantity' => $quantityToUse,
                        ]);

                        $batch->increment('sold_quantity', $quantityToUse);
                        $remainingQuantity -= $quantityToUse;

                        $totalPrice += $quantityToUse * $sparepart->final_selling_price;
                    }
                } elseif ($item['item_type'] === 'service') {
                    $service = Service::find($item['item_id']);
                    if (!$service) {
                        throw new Exception("Service dengan ID {$item['item_id']} tidak ditemukan.");
                    }

                    $transaction->items()->create([
                        'item_type' => 'service',
                        'item_id' => $service->id,
                        'price' => $service->harga_standar,
                        'quantity' => 1,
                    ]);

                    $totalPrice += $service->harga_standar;
                }
            }

            $transaction->update(['total_price' => $totalPrice]);

            DB::commit();

            // === NOTIFIKASI PENJUALAN ===
            $customerName = optional($transaction->customer)->name ?? 'Kustomer';
            $this->notificationService->saleCreated($transaction->invoice_number, $customerName);

            // === CEK STOK HABIS/MENIPIS ===
            $soldSparepartIds = $transaction->items()
                ->where('item_type', 'sparepart')
                ->pluck('item_id')
                ->unique();

            foreach ($soldSparepartIds as $sid) {
                $sp = Sparepart::find($sid);
                if (!$sp) continue;

                $availableAfter = PurchaseOrderItem::where('sparepart_id', $sp->id)
                    ->selectRaw('COALESCE(SUM(quantity - sold_quantity),0) as available_stock')
                    ->where(function ($q) {
                        $q->where('expired_date', '>=', Carbon::today())
                          ->orWhereNull('expired_date');
                    })
                    ->value('available_stock');

                $minStock = $sp->min_stock ?? null;
                $thresholdHit = $minStock !== null ? ($availableAfter <= $minStock) : ($availableAfter <= 0);

                if ($thresholdHit) {
                    $this->notificationService->stockLow($sp->name, (int)$availableAfter, $minStock);
                }
            }

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengembalikan stok sparepart dari transaksi (untuk update/hapus).
     */
    public function restoreStockFromTransaction(Transaction $transaction): void
    {
        foreach ($transaction->items as $item) {
            if ($item->item_type === 'sparepart') {
                $purchaseOrderItem = $item->purchaseOrderItem;
                if ($purchaseOrderItem) {
                    $purchaseOrderItem->decrement('sold_quantity', $item->quantity);
                }
            }
        }
    }

    /**
     * Update transaksi + hitung ulang total_price.
     */
    public function updateTransaction(Transaction $transaction, array $transactionData, array $itemsData)
    {
        DB::beginTransaction();
        try {
            // Kembalikan stok lama
            $this->restoreStockFromTransaction($transaction);

            // Update transaksi utama (kecuali total_price dulu)
            $transaction->update($transactionData);

            // Hapus semua item lama
            $transaction->items()->delete();

            $totalPrice = 0;

            // Tambahkan item baru dengan logika FEFO/FIFO
            foreach ($itemsData as $item) {
                if ($item['item_type'] === 'sparepart') {
                    $sparepart = Sparepart::find($item['item_id']);
                    if (!$sparepart) throw new Exception("Sparepart ID {$item['item_id']} tidak ditemukan.");
                    $requestedQuantity = $item['quantity'];

                    $availableStock = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                        ->selectRaw('SUM(quantity - sold_quantity) as available_stock')
                        ->where(function ($query) {
                            $query->where('expired_date', '>=', Carbon::today())
                                  ->orWhereNull('expired_date');
                        })
                        ->value('available_stock') ?? 0;

                    if ($availableStock < $requestedQuantity) {
                        throw new Exception("Stok untuk sparepart '{$sparepart->name}' tidak mencukupi. Sisa: {$availableStock}");
                    }

                    $batches = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                        ->whereRaw('quantity - sold_quantity > 0')
                        ->where(function ($query) {
                            $query->where('expired_date', '>=', Carbon::today())
                                  ->orWhereNull('expired_date');
                        })
                        ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date ASC, created_at ASC')
                        ->get();

                    $remainingQuantity = $requestedQuantity;
                    foreach ($batches as $batch) {
                        if ($remainingQuantity <= 0) break;
                        $availableQty = $batch->quantity - $batch->sold_quantity;
                        if ($availableQty <= 0) continue;

                        $quantityToUse = min($remainingQuantity, $availableQty);

                        $transaction->items()->create([
                            'item_type' => 'sparepart',
                            'item_id' => $sparepart->id,
                            'purchase_order_item_id' => $batch->id,
                            'price' => $sparepart->final_selling_price,
                            'quantity' => $quantityToUse,
                        ]);

                        $batch->increment('sold_quantity', $quantityToUse);
                        $remainingQuantity -= $quantityToUse;

                        $totalPrice += $quantityToUse * $sparepart->final_selling_price;
                    }
                } elseif ($item['item_type'] === 'service') {
                    $service = Service::find($item['item_id']);
                    if (!$service) throw new Exception("Service ID {$item['item_id']} tidak ditemukan.");

                    $transaction->items()->create([
                        'item_type' => 'service',
                        'item_id' => $service->id,
                        'price' => $service->harga_standar,
                        'quantity' => 1,
                    ]);

                    $totalPrice += $service->harga_standar;
                }
            }

            // Update total harga setelah item baru selesai diproses
            $transaction->update(['total_price' => $totalPrice]);

            DB::commit();
            return $transaction->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}