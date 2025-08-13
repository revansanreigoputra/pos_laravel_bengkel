<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Kirim notifikasi ke semua user dengan role tertentu.
     * @param array $roles contoh: ['admin','kasir']
     * @param string $type 'purchase'|'sale'|'stock_alert'
     * @param string $message pesan ringkas
     * @param array|null $data data tambahan (opsional, tidak dipakai di UI)
     */
    public function notifyRoles(array $roles, string $type, string $message, ?array $data = null): void
    {
        // Ambil user yang punya salah satu role tsb
        $users = User::role($roles)->get(['id']);

        DB::transaction(function () use ($users, $type, $message, $data) {
            foreach ($users as $user) {
                Notification::create([
                    'type'            => $type,
                    'notifiable_type' => User::class,
                    'notifiable_id'   => $user->id,
                    'message'         => $message,
                    'data'            => $data,
                ]);
            }
        });
    }

    public function purchaseCreated(string $invoiceNumber, string $supplierName, ?string $status = null): void
    {
        $statusText = $status ? " (status: {$status})" : '';
        $this->notifyRoles(['admin','kasir'], 'purchase', "PO #{$invoiceNumber} dari {$supplierName}{$statusText} dibuat.", [
            'invoice_number' => $invoiceNumber,
            'supplier' => $supplierName,
            'status' => $status,
        ]);
    }

    public function saleCreated(string $invoiceNumber, string $customerName): void
    {
        $this->notifyRoles(['admin','kasir'], 'sale', "Penjualan #{$invoiceNumber} untuk {$customerName} dibuat.", [
            'invoice_number' => $invoiceNumber,
            'customer' => $customerName,
        ]);
    }

    public function stockExpired(string $sparepartName, int $qty): void
    {
        $this->notifyRoles(['admin','kasir'], 'stock_alert', "Stok kedaluwarsa: {$sparepartName} sisa {$qty} unit di batch kedaluwarsa.");
    }

    public function stockLow(string $sparepartName, int $available, ?int $minStock = null): void
    {
        $tail = $minStock !== null ? " (min: {$minStock})" : '';
        $this->notifyRoles(['admin','kasir'], 'stock_alert', "Stok menipis/habis: {$sparepartName} tersedia {$available}{$tail}.");
    }
}