<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class ReportController extends Controller
{
    /**
     * Display the transaction report page.
     */
    public function transactionReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::query()->where('status', 'completed');

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
}