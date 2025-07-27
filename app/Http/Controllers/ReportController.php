<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transactionReport(Request $request)
    {
        $query = Transaction::with(['items.service', 'items.sparepart'])
                            ->where('status', 'completed'); // Filter hanya transaksi yang 'completed'
        
        // Anda bisa menambahkan filter tanggal jika diperlukan
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->latest()->get();

        return view('pages.report.transaction', compact('transactions'));
    }

    // Jika Anda ingin menambahkan fungsi laporan pembelian:
    // public function purchaseReport(Request $request)
    // {
    //     // Logika untuk laporan pembelian
    // }
}