<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('type', 'monthly'); // 'Bulanan' atau 'Mingguan'
        $year = $request->input('year', Carbon::now()->year);   
        $month = $request->input('month', Carbon::now()->month);
        $week = $request->input('week', Carbon::now()->weekOfYear);

        $transactions = Transaction::where('status', 'completed');

        if ($reportType === 'monthly') {
            $transactions->whereYear('transaction_date', $year)
                         ->whereMonth('transaction_date', $month);
        } elseif ($reportType === 'weekly') {
            $transactions->whereYear('transaction_date', $year)
                         ->whereBetween('transaction_date', [
                             Carbon::parse("{$year}-01-01")->startOfWeek()->addWeeks($week - 1)->startOfDay(),
                             Carbon::parse("{$year}-01-01")->endOfWeek()->addWeeks($week - 1)->endOfDay()
                         ]);
        }

        $transactions = $transactions->orderBy('transaction_date', 'desc')->paginate(10); // Paginate for better performance

        // Calculate totals
        $totalTransactions = $transactions->total();
        $totalRevenue = $transactions->sum(function($transaction) {
            return $transaction->total_price - $transaction->discount_amount;
        });

        // For month/year selection in the view
        $availableYears = Transaction::selectRaw('YEAR(transaction_date) as year')
                                      ->distinct()
                                      ->orderBy('year', 'desc')
                                      ->pluck('year');

        // For week selection in the view (for the selected year)
        $availableWeeks = [];
        if ($reportType === 'weekly') {
            $startDateOfYear = Carbon::parse("{$year}-01-01");
            $weeksInYear = $startDateOfYear->weeksInYear;
            for ($i = 1; $i <= $weeksInYear; $i++) {
                $availableWeeks[] = $i;
            }
        }


        return view('reports.transactions', compact(
            'transactions',
            'reportType',
            'year',
            'month',
            'week',
            'totalTransactions',
            'totalRevenue',
            'availableYears',
            'availableWeeks'
        ));
    }
}