<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaction;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Exports\PurchaseOrdersExport; 
use App\Exports\SparepartReportExport;
class ReportController extends Controller
{
    /**
     * Halaman laporan pembelian
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('order_date', [$start, $end]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->with('supplier')
            ->orderBy('order_date', 'desc')
            ->get();

        return view('report.purchase.index', compact('purchaseOrders'));
    }

    /**
     * Halaman Laporan Penjualan (Transaksi)
     */
    public function transactionReport(Request $request)
    {
        $query = Transaction::query();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->with(['customer'])->get();

        return view('pages.report.transaction', compact('transactions'));
    }


    /**
     * Halaman laporan stok sparepart
     */
    public function stockReport(Request $request)
    {
        // Get the current tab from the request, default to 'available'
        $activeTab = $request->get('tab', 'available');

        // Get the date range from the request
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $paymentMethod = $request->get('payment_method'); // Tambahkan ini

        // Start a query on the Sparepart model
        $query = Sparepart::query();

        // Eager load the purchaseOrderItems relationship
        $query->with(['purchaseOrderItems' => function ($itemQuery) use ($startDate, $endDate, $paymentMethod) {
            $itemQuery->orderBy('expired_date', 'asc')
                ->orderBy('created_at', 'asc');

            // Apply date filter to purchase order items if dates are provided
            if ($startDate && $endDate) {
                $itemQuery->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            }

            // Filter berdasarkan metode pembayaran
            if ($paymentMethod) {
                $itemQuery->whereHas('purchaseOrder', function ($q) use ($paymentMethod) {
                    $q->where('payment_method', $paymentMethod);
                });
            }
        }]);

        // Apply filters based on the active tab
        if ($activeTab === 'available') {
            // Tampilkan semua sparepart termasuk yang stok 0 untuk konsistensi dengan index
            $spareparts = $query->paginate(10);
        } elseif ($activeTab === 'expired') {
            // Filter for expired stock
            $spareparts = $query->whereHas('purchaseOrderItems', function ($itemQuery) {
                $itemQuery->whereRaw('quantity - sold_quantity > 0')
                    ->whereNotNull('expired_date')
                    ->where('expired_date', '<', Carbon::today());
            })->paginate(10);
        } elseif ($activeTab === 'empty') {
            // Filter for empty stock
            $spareparts = $query->whereDoesntHave('purchaseOrderItems')
                ->orWhereHas('purchaseOrderItems', function ($itemQuery) {
                    $itemQuery->whereRaw('quantity - sold_quantity <= 0');
                })
                ->paginate(10);
        } else {
            // Default to 'available' if the tab is not recognized
            $spareparts = $query->paginate(10);
        }

        return view('pages.report.sparepart-report', compact('spareparts', 'activeTab', 'startDate', 'endDate', 'paymentMethod'));
    }
    /**
     * Laporan stok sparepart: export PDF report based on the selected tab and filters.
     */
    public function exportPdfSparepartStock(Request $request)
    {
        $tab = $request->get('tab', 'available');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $reportTitle = 'Laporan Stok Sparepart';

        $spareparts = Sparepart::with(['category', 'supplier', 'purchaseOrderItems'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        if ($tab === 'available') {
            $spareparts = $spareparts->filter(fn($s) => $s->available_stock > 0);
            $reportTitle = 'Laporan Stok Sparepart Tersedia';
        } elseif ($tab === 'expired') {
            $spareparts = $spareparts->filter(function ($s) {
                return $s->purchaseOrderItems->whereNotNull('expired_date')
                    ->where('expired_date', '<', now())
                    ->where('quantity', '>', 0)
                    ->isNotEmpty();
            });
            $reportTitle = 'Laporan Stok Sparepart Kadaluarsa';
        } elseif ($tab === 'empty') {
            $spareparts = $spareparts->filter(fn($s) => $s->available_stock <= 0);
            $reportTitle = 'Laporan Stok Sparepart Kosong';
        }

        $pdf = Pdf::loadView('pages.report.exportPDF-sparepart', [
            'spareparts' => $spareparts,
            'tab' => $tab,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportTitle' => $reportTitle,
        ]);

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $fileName = "{$timestamp}_Laporan sparepart {$tab}.pdf";
        return $pdf->download($fileName);
    }

    /**
     * Export laporan transaksi ke Excel
     */
    public function exportTransactionReport(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $status = $request->status;

        return Excel::download(
            new TransactionsExport($start, $end, $status),
            'laporan_transaksi.xlsx'
        );
    }

    /**
     * Halaman Laporan Pembelian (Purchase Order)
     */
    public function purchaseReport(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $status = $request->status;
        $paymentMethod = $request->payment_method;

        $query = PurchaseOrder::query()->with('supplier');

        if ($start && $end) {
            $query->whereBetween('order_date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $purchaseOrders = $query->latest()->get();

        return view('pages.report.purchase', compact('purchaseOrders', 'start', 'end', 'status', 'paymentMethod'));
    }

    /**
     * Export laporan pembelian ke Excel
     */
    public function exportPurchaseExcel(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $status = $request->status;
        $paymentMethod = $request->payment_method;

        $query = \App\Models\PurchaseOrder::query()->with(['items.sparepart', 'supplier']);

        if ($start && $end) {
            $query->whereBetween('order_date', [
                \Carbon\Carbon::parse($start)->startOfDay(),
                \Carbon\Carbon::parse($end)->endOfDay()
            ]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $purchaseOrders = $query->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PurchaseOrdersExport(
                $purchaseOrders,
                'Laporan Pembelian',
                $start,
                $end,
                $status,
                $paymentMethod
            ),
            'purchase_orders.xlsx'
        );
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');
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

        return Excel::download(
            new TransactionsExport($startDate, $endDate, $status),
            $filename
        );
    }

    public function exportPurchaseReport(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $status = $request->status;
        $paymentMethod = $request->payment_method;

        $query = PurchaseOrder::query();

        if ($start && $end) {
            $query->whereBetween('order_date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $purchaseOrders = $query->get();

        return Excel::download(
            new PurchaseOrdersExport(
                $purchaseOrders,
                'Laporan Pembelian',
                $start,
                $end,
                $status,
                $paymentMethod
            ),
            'purchase_orders.xlsx'
        );
    }
    // excel export in sparepart report
    public function ExportSparepartReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $fileName = "{$timestamp}_Laporan Stok Sparepart.xlsx";

        return Excel::download(new SparepartReportExport($startDate, $endDate), $fileName);
    }
    
}