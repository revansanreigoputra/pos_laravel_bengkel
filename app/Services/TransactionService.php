<?php

namespace App\Services;

use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\PurchaseOrderItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;

class TransactionService
{
    /**
     * Membuat transaksi dan mengurangi stok sparepart dengan logika FIFO/FEFO.
     *
     * @param array 
     * @param array 
     * @return Transaction
     * @throws Exception
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

                    if ($sparepart->available_stock < $requestedQuantity) {
                        throw new Exception("Stok untuk sparepart '{$sparepart->name}' tidak mencukupi. Stok tersedia: {$sparepart->available_stock}");
                    }

                    // Ambil batch stok dengan urutan FEFO atau FIFO
                    $batches = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                        ->where('quantity', '>', 0)
                        ->where(function ($query) {
                            $query->where('expired_date', '>=', Carbon::today())
                                ->orWhereNull('expired_date');
                        })
                        ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date ASC, created_at ASC')
                        ->get();

                    $remainingQuantity = $requestedQuantity;
                    foreach ($batches as $batch) {
                        if ($remainingQuantity <= 0) {
                            break;
                        }

                        $quantityToUse = min($remainingQuantity, $batch->quantity);

                        $transaction->items()->create([
                            'item_type' => 'sparepart',
                            'item_id' => $sparepart->id,
                            'purchase_order_item_id' => $batch->id,
                            'price' => $sparepart->final_selling_price,
                            'quantity' => $quantityToUse,
                        ]);

                        $batch->decrement('quantity', $quantityToUse);
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

            return $transaction;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengembalikan stok dari item-item transaksi yang akan dihapus atau di-update.
     *
     * @param Transaction $transaction
     */
    public function restoreStockFromTransaction(Transaction $transaction): void
    {
        foreach ($transaction->items as $item) {
            if ($item->item_type === 'sparepart') {
                $purchaseOrderItem = $item->purchaseOrderItem;
                if ($purchaseOrderItem) {
                    $purchaseOrderItem->increment('quantity', $item->quantity);
                }
            }
        }
    }
}