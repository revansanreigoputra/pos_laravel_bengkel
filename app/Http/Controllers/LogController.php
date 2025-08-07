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

        $spareparts = Sparepart::all()->map(function ($sparepart) use ($startDate, $endDate) {
            $stok_awal = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                ->whereHas('purchaseOrder', fn($q) => $q->where('order_date', '<', $startDate))
                ->sum('quantity');

            $barang_masuk = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                ->whereHas('purchaseOrder', fn($q) => $q->whereBetween('order_date', [$startDate, $endDate]))
                ->sum('quantity');

            $barang_keluar = TransactionItem::where('item_type', 'sparepart')
                ->where('item_id', $sparepart->id)
                ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
                ->sum('quantity');

            return (object) [
                'name' => $sparepart->name,
                'code_part' => $sparepart->code_part,
                'stok_awal' => $stok_awal,
                'barang_masuk' => $barang_masuk,
                'barang_keluar' => $barang_keluar,
                'stok_akhir' => $stok_awal + $barang_masuk - $barang_keluar,
            ];
        });

        return view('logs.sparepart', compact('spareparts', 'startDate', 'endDate'));
    }
}
