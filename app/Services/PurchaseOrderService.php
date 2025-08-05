<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderService
{
    /**
     * Membuat purchase order dan item-nya.
     *
     * @param array $purchaseOrderData
     * @param array $itemsData
     * @return PurchaseOrder
     */
    public function createPurchaseOrder(array $purchaseOrderData, array $itemsData): PurchaseOrder
    {
        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

            foreach ($itemsData as $itemData) {
                $purchaseOrder->items()->create($itemData);
            }

            DB::commit();
            return $purchaseOrder;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengembalikan stok dari item-item yang terkait dengan Purchase Order.
     * Logika ini akan digunakan saat Purchase Order dibatalkan atau dihapus.
     *
     * @param PurchaseOrder $purchaseOrder
     */
    public function revertPurchaseOrderItems(PurchaseOrder $purchaseOrder): void
    {
        
    }
}