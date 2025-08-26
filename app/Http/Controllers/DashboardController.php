<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\PurchaseOrder;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $categoryCount = Category::count();
        $customerCount = Customer::count();
        $supplierCount = Supplier::count();
        $userCount = User::count();

        // --- Data untuk Grafik Pendapatan Bulanan ---
        // Mengambil total pendapatan per bulan selama 12 bulan terakhir
        $monthlyIncome = Transaction::select(
            DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
            DB::raw('SUM(total_price) as total_income')
        )
            ->where('transaction_date', '>=', now()->subMonths(11)->startOfMonth()) // 12 bulan terakhir
            ->where('status', 'completed') // Hanya transaksi yang selesai
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $incomeAmounts = [];
        // Isi bulan yang kosong dengan 0 pendapatan
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->translatedFormat('F Y'); // Nama bulan lengkap
            $income = $monthlyIncome->where('month', $month)->first();
            $incomeAmounts[] = $income ? $income->total_income : 0;
        }

    // --- Data untuk Grafik Pengeluaran Bulanan ---
    $monthlyExpenses = PurchaseOrder::select(
        DB::raw('DATE_FORMAT(order_date, "%Y-%m") as month'),
        DB::raw('SUM(total_price) as total_expenses')
    )
        ->where('order_date', '>=', now()->subMonths(11)->startOfMonth()) // 12 bulan terakhir
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $expenseCounts = [];
    // Isi bulan yang kosong dengan 0 pengeluaran
    for ($i = 11; $i >= 0; $i--) {
        $month = now()->subMonths($i)->format('Y-m');
        $count = $monthlyExpenses->where('month', $month)->first();
        $expenseCounts[] = $count ? $count->total_expenses : 0;
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
                $service = Service::find($item->item_id); // Menggunakan namespace penuh
                $name = $service ? $service->nama : 'Layanan Tidak Dikenal';
            } elseif ($item->item_type === 'sparepart') {
                $sparepart =  Sparepart::find($item->item_id); // Menggunakan namespace penuh
                $name = $sparepart ? $sparepart->name : 'Sparepart Tidak Dikenal';
            }
            $itemLabels[] = $name;
            $itemQuantities[] = $item->total_quantity_sold;
        }
        // data Ambil 5 pesanan pembelian terbaru
        $recentPurchaseOrders = PurchaseOrder::with(['supplier', 'items'])
            ->latest('order_date')
            ->take(5)
            ->get();
        //data Ambil 5 transaksi terbaru
        $recentTransactions = Transaction::with(['customer', 'items'])
            ->latest('transaction_date')
            ->take(5)
            ->get();
        // chart untuk laporan sparepart start
        $allSpareparts = Sparepart::with('purchaseOrderItems')->get();

        $availableStockCount = $allSpareparts->where('available_stock', '>', 0)->count();
        $emptyStockCount = $allSpareparts->where('available_stock', '<=', 0)->count();

        // perlu menghitung stok kadaluarsa dari purchaseOrderItems
        $expiredStockCount = 0;
        foreach ($allSpareparts as $sparepart) {
            $expiredItems = $sparepart->purchaseOrderItems
                ->where('expired_date', '<', Carbon::today())
                ->where('quantity', '>', 0);
            if ($expiredItems->count() > 0) {
                $expiredStockCount++;
            }
        }

        // Siapkan data untuk bagan
        $sparepartStockChartData = [
            'labels' => ['Stok Tersedia', 'Stok Kosong', 'Stok Kadaluarsa'],
            'data' => [$availableStockCount, $emptyStockCount, $expiredStockCount],
            'colors' => ['#22c55e', '#ffc107', '#dc3545']
        ];
        // end chart laporan sparepart

        // start chart laporan transaksi
        // --- Data untuk Grafik Laporan Penjualan (1 bulan terakhir) ---
        $salesData = Transaction::select(
            DB::raw('DATE(transaction_date) as date'),
            DB::raw('SUM(total_price) as total_sales')
        )
            ->where('status', 'completed')
            ->where('transaction_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $days = [];
        $salesAmounts = [];
        $period = Carbon::now()->subDays(29)->startOfDay();

        while ($period->lte(Carbon::now()->startOfDay())) {
            $date = $period->toDateString();
            $days[] = Carbon::parse($date)->format('d M'); // Format tanggal: 05 Aug

            $sales = $salesData->firstWhere('date', $date);
            $salesAmounts[] = $sales ? $sales->total_sales : 0;

            $period->addDay();
        }

        $monthlySalesChartData = [
            'labels' => $days,
            'data' => $salesAmounts,
        ];
        // end chart laporan transaksi
        return view('pages.dashboard', compact(
            'categoryCount',
            'customerCount',
            'supplierCount',
            'userCount',
            'months',          // grafik pendapatan bulanan
            'incomeAmounts',   // grafik pendapatan bulanan
            'expenseCounts',   // grafik pengeluaran bulanan
            'itemLabels',      // grafik item teratas
            'itemQuantities',  // grafik item teratas
            'recentPurchaseOrders', // tabel pesanan pembelian terbaru
            'recentTransactions', // tabel transaksi terbaru
            'sparepartStockChartData',
            'monthlySalesChartData'
        ));
    }
}