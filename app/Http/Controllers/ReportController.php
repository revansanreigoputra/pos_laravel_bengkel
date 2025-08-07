<?php

namespace App\Http\Controllers;
use App\Exports\PurchaseOrdersExport; 
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseOrderItem; // Import model PurchaseOrderItem
use Illuminate\Http\Request;
use Carbon\Carbon; 
use App\Models\Sparepart; // Import model Sparepart
use App\Exports\TransactionsExport; 

class ReportController extends Controller
{
    /**
     * Display the transaction report page.
     * Menampilkan halaman laporan transaksi (penjualan).
     */
    public function transactionReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::query()->where('status', 'completed')->with('customer');

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $transactions = $query->with(['items.service', 'items.sparepart'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $cardQuery = Transaction::query();
        if ($startDate) {
            $cardQuery->whereDate('transaction_date', '>=', $startDate);
        }
        if ($endDate) {
            $cardQuery->whereDate('transaction_date', '<=', $endDate);
        }

        $pendingTransactionsCount = (clone $cardQuery)->where('status', 'pending')->count();
        $completedTransactionsCount = (clone $cardQuery)->where('status', 'completed')->count();
        $cancelledTransactionsCount = (clone $cardQuery)->where('status', 'cancelled')->count();
        $totalRevenue = (clone $cardQuery)->where('status', 'completed')->sum('total_price');

        return view('pages.report.transaction', compact(
            'transactions',
            'pendingTransactionsCount',
            'completedTransactionsCount',
            'cancelledTransactionsCount',
            'totalRevenue'
        ));
    }

    /**
     * Display the stock report page.
     * Menampilkan halaman laporan stok sparepart.
     * Ini adalah metode baru yang diperlukan.
     */
    public function stockReport()
    {
        // Mendapatkan semua sparepart dengan semua item pembelian terkait.
        // Tidak ada filter berdasarkan kuantitas atau tanggal kadaluarsa di sini,
        // sehingga semua item pembelian akan dimuat.
        $spareparts = Sparepart::with(['purchaseOrderItems' => function ($query) {
            $query->orderBy('expired_date', 'asc')
                ->orderBy('created_at', 'asc');
        }])->paginate(10); // ← tambahkan paginate di sini


        return view('pages.report.sparepart-report', compact('spareparts'));
    }

    /**
     * Display the expired stock report page.
     * Menampilkan halaman laporan stok yang kadaluarsa.
     * Ini adalah metode baru yang sangat penting untuk sistem FIFO/FEFO.
     */
    public function expiredStockReport()
    {
        $expiredItems = PurchaseOrderItem::where('quantity', '>', 0)
            ->whereNotNull('expired_date')
            ->where('expired_date', '<', Carbon::today())
            ->with('sparepart', 'purchaseOrder')
            ->get();

        return view('pages.report.expired_stock', compact('expiredItems'));
    }

    /**
     * Export transactions to Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $exportTitle = $request->query('export_title', 'Laporan_Transaksi');

        $filename = $exportTitle;
        if ($startDate && $endDate) {
            $filename .= '_dari_' . Carbon::parse($startDate)->format('Ymd') . '_sampai_' . Carbon::parse($endDate)->format('Ymd');
        } elseif ($startDate) {
            $filename .= '_dari_' . Carbon::parse($startDate)->format('Ymd');
        } elseif ($endDate) {
            $filename .= '_sampai_' . Carbon::parse($endDate)->format('Ymd');
        }
        $filename .= '.xlsx';

        return Excel::download(new TransactionsExport($startDate, $endDate, $exportTitle), $filename);
    }

    public function purchaseReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PurchaseOrder::query();

        if ($startDate) {
            $query->whereDate('order_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('order_date', '<=', $endDate);
        }

        $purchaseOrders = $query->with('supplier')
            ->orderBy('order_date', 'desc')
            ->get();

        // Separate logic for summary cards
        $cardQuery = PurchaseOrder::query();
        if ($startDate) {
            $cardQuery->whereDate('order_date', '>=', $startDate);
        }
        if ($endDate) {
            $cardQuery->whereDate('order_date', '<=', $endDate);
        }

        $pendingPurchaseCount = (clone $cardQuery)->where('status', 'pending')->count();
        $receivedPurchaseCount = (clone $cardQuery)->where('status', 'received')->count();
        $canceledPurchaseCount = (clone $cardQuery)->where('status', 'canceled')->count();
        $totalPurchaseCost = (clone $cardQuery)->where('status', 'received')->sum('total_price');

        return view('pages.report.purchase', compact(
            'purchaseOrders',
            'pendingPurchaseCount',
            'receivedPurchaseCount',
            'canceledPurchaseCount',
            'totalPurchaseCost'
        ));
    }

    // // report excel purchase
    // public function exportPurchaseExcel(Request $request)
    // {
    //     $startDate = $request->query('start_date');
    //     $endDate = $request->query('end_date');
    //     $exportTitle = $request->query('export_title', 'Laporan_Pembelian');

    //     $filename = $exportTitle;
    //     if ($startDate && $endDate) {
    //         $filename .= '_dari_' . Carbon::parse($startDate)->format('Ymd') . '_sampai_' . Carbon::parse($endDate)->format('Ymd');
    //     } elseif ($startDate) {
    //         $filename .= '_dari_' . Carbon::parse($startDate)->format('Ymd');
    //     } elseif ($endDate) {
    //         $filename .= '_sampai_' . Carbon::parse($endDate)->format('Ymd');
    //     }
    //     $filename .= '.xlsx';

    //     // Use a new export class for purchase orders
    //     return Excel::download(new PurchaseOrdersExport($startDate, $endDate, $exportTitle), $filename);
    // }
}
