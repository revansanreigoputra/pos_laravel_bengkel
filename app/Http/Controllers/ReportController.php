<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use App\Exports\PurchaseOrdersExport;

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
        $start = $request->start_date;
        $end = $request->end_date;
        $status = $request->status;

        $query = Transaction::query()
            ->with(['customer', 'items.sparepart', 'items.service']);

        if ($start && $end) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->get();

        return view('pages.report.transaction', compact('transactions', 'start', 'end', 'status'));
    }

    /**
     * Halaman laporan stok sparepart
     */
    public function stockReport()
    {
        $spareparts = Sparepart::with(['purchaseOrderItems' => function ($query) {
            $query->orderBy('expired_date', 'asc')
                ->orderBy('created_at', 'asc');
        }])->paginate(10);

        return view('pages.report.sparepart-report', compact('spareparts'));
    }

    /**
     * Halaman laporan stok expired
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

        return Excel::download(
            new PurchaseOrdersExport($start, $end, $status),
            'laporan_pembelian.xlsx'
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
}
