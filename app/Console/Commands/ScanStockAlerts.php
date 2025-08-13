<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrderItem;
use App\Models\Sparepart;
use Illuminate\Support\Carbon;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ScanStockAlerts extends Command
{
    protected $signature = 'stock:scan-alerts';
    protected $description = 'Scan stok kedaluwarsa dan stok habis/menipis, kirim notifikasi';

    public function handle(NotificationService $notifier): int
    {
        // Kedaluwarsa: batch expired dan masih ada sisa (quantity - sold_quantity > 0)
        $expired = PurchaseOrderItem::query()
            ->whereNotNull('expired_date')
            ->whereDate('expired_date', '<', Carbon::today())
            ->whereRaw('quantity - sold_quantity > 0')
            ->with('sparepart')
            ->get();

        foreach ($expired as $batch) {
            $spName = optional($batch->sparepart)->name ?? 'Sparepart';
            $remain = max(0, ($batch->quantity - $batch->sold_quantity));
            if ($remain > 0) {
                $notifier->stockExpired($spName, (int)$remain);
            }
        }

        // Stok habis/menipis: hitung dari batch (konsisten dgn transaksi)
        $sparepartIds = PurchaseOrderItem::query()->distinct()->pluck('sparepart_id');
        foreach ($sparepartIds as $sid) {
            $sp = Sparepart::find($sid);
            if (!$sp) continue;

            $available = PurchaseOrderItem::where('sparepart_id', $sp->id)
                ->selectRaw('COALESCE(SUM(quantity - sold_quantity),0) as available_stock')
                ->where(function ($q) {
                    $q->whereNull('expired_date')->orWhere('expired_date', '>=', Carbon::today());
                })
                ->value('available_stock');

            $minStock = $sp->min_stock ?? null;
            $thresholdHit = $minStock !== null ? ($available <= $minStock) : ($available <= 0);

            if ($thresholdHit) {
                $notifier->stockLow($sp->name, (int)$available, $minStock);
            }
        }

        $this->info('Scan selesai.');
        return self::SUCCESS;
    }
}