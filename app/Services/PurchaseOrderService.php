<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\NotificationService;

class PurchaseOrderService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService) 
    {
        $this->notificationService = $notificationService;
    }

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

            // === NOTIFIKASI (tambahan) ===
            $supplierName = optional($purchaseOrder->supplier)->name ?? 'Supplier';
            $this->notificationService->purchaseCreated(
                $purchaseOrder->invoice_number,
                $supplierName,
                $purchaseOrder->status ?? null
            );

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
        //
    }
}