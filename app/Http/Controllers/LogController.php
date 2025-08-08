<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Transaction;
use App\Models\Sparepart;
use App\Models\PurchaseOrderItem;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LogController extends Controller
{
    public function logPembelian()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items.sparepart'])->latest()->paginate(10);
        return view('logs.pembelian', compact('purchaseOrders'));
    }

    public function logPenjualan()
    {
        $transactions = Transaction::with(['customer', 'items.sparepart', 'items.service'])->latest()->paginate(10);
        return view('logs.penjualan', compact('transactions'));
    }

    public function logPergerakanStok()
    {
        $spareparts = Sparepart::with(['purchaseOrderItems', 'transactionItems'])->paginate(10);
        return view('logs.stok', compact('spareparts'));
    }

    public function logSparepart(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();
        $jenis = $request->input('jenis');

        // Barang Masuk (Purchase)
        $barangMasuk = \App\Models\PurchaseOrderItem::with(['purchaseOrder.supplier', 'sparepart'])
            ->whereHas('purchaseOrder', fn($q) => $q->whereBetween('order_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->purchaseOrder->invoice_number ?? '-',
                    'supplier' => $item->purchaseOrder->supplier->name ?? '-',
                    'tanggal_masuk' => $item->purchaseOrder->order_date ? $item->purchaseOrder->order_date->format('Y-m-d') : '-',
                    'jenis' => 'Barang Masuk',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Barang Keluar (Transaction)
        $barangKeluar = \App\Models\TransactionItem::with(['transaction.customer', 'sparepart'])
            ->where('item_type', 'sparepart')
            ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->transaction->invoice_number ?? '-',
                    'customer' => $item->transaction->customer->name ?? '-',
                    'tanggal_keluar' => $item->transaction->transaction_date ? $item->transaction->transaction_date->format('Y-m-d') : '-',
                    'jenis' => 'Barang Keluar',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Filter sesuai pilihan
        if ($jenis == 'masuk') {
            $data = $barangMasuk->values();
        } elseif ($jenis == 'keluar') {
            $data = $barangKeluar->values();
        } else {
            $data = $barangMasuk->concat($barangKeluar)->values();
        }

        return view('logs.sparepart', compact('data'));
    }

    public function logSparepartDetail(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        // Barang Masuk (Purchase)
        $barangMasuk = \App\Models\PurchaseOrderItem::with(['purchaseOrder.supplier', 'sparepart'])
            ->whereHas('purchaseOrder', fn($q) => $q->whereBetween('order_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->purchaseOrder->invoice_number ?? '-',
                    'supplier' => $item->purchaseOrder->supplier->name ?? '-',
                    'customer' => null,
                    'jenis' => 'Barang Masuk',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Barang Keluar (Transaction)
        $barangKeluar = \App\Models\TransactionItem::with(['transaction.customer', 'sparepart'])
            ->where('item_type', 'sparepart')
            ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->transaction->invoice_number ?? '-',
                    'supplier' => null,
                    'customer' => $item->transaction->customer->name ?? '-',
                    'jenis' => 'Barang Keluar',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Gabungkan dan urutkan
        $data = $barangMasuk->concat($barangKeluar)->values();

        return view('logs.sparepart_detail', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
