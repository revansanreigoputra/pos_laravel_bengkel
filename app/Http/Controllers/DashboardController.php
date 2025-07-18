<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Transaction; // Tambahkan ini
use App\Models\TransactionItem; // Tambahkan ini
use Illuminate\Support\Facades\DB; // Tambahkan ini

class DashboardController extends Controller
{
    public function index()
    {
        $categoryCount = Category::count();
        $customerCount = Customer::count();
        $supplierCount = Supplier::count();
        $userCount = User::count();

        // --- Data untuk Grafik Transaksi Bulanan ---
        // Mengambil total transaksi per bulan selama 12 bulan terakhir
        $monthlyTransactions = Transaction::select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->where('transaction_date', '>=', now()->subMonths(11)->startOfMonth()) // 12 bulan terakhir
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $transactionCounts = [];
        // Isi bulan yang kosong dengan 0 transaksi
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->translatedFormat('F Y'); // Nama bulan lengkap
            $count = $monthlyTransactions->where('month', $month)->first();
            $transactionCounts[] = $count ? $count->total_transactions : 0;
        }

        // --- Data untuk Grafik Penjualan Item Teratas ---
        $topSellingItems = TransactionItem::select(
                'item_type',
                'item_id',
                DB::raw('SUM(quantity) as total_quantity_sold')
            )
            ->groupBy('item_type', 'item_id')
            ->orderByDesc('total_quantity_sold')
            ->limit(5) // Ambil 5 item teratas
            ->get();

        $itemLabels = [];
        $itemQuantities = [];

        foreach ($topSellingItems as $item) {
            $name = '';
            if ($item->item_type === 'service') {
                $service = \App\Models\Service::find($item->item_id); // Menggunakan namespace penuh
                $name = $service ? $service->nama : 'Layanan Tidak Dikenal';
            } elseif ($item->item_type === 'sparepart') {
                $sparepart = \App\Models\Sparepart::find($item->item_id); // Menggunakan namespace penuh
                $name = $sparepart ? $sparepart->name : 'Sparepart Tidak Dikenal';
            }
            $itemLabels[] = $name;
            $itemQuantities[] = $item->total_quantity_sold;
        }


        return view('pages.dashboard', compact(
            'categoryCount',
            'customerCount',
            'supplierCount',
            'userCount',
            'months',          // Untuk grafik transaksi bulanan
            'transactionCounts', // Untuk grafik transaksi bulanan
            'itemLabels',      // Untuk grafik item teratas
            'itemQuantities'   // Untuk grafik item teratas
        ));
    }
}