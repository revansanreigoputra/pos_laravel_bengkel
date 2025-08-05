<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    /**
     * Menampilkan laporan ringkasan stok.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data sparepart dan menghitung total kuantitasnya dari purchase_order_items
        $spareparts = Sparepart::withSum('purchaseOrderItems', 'quantity')
                                ->orderBy('name')
                                ->get();

        // Menghitung total stok keseluruhan
        $totalStock = $spareparts->sum('purchase_order_items_sum_quantity');

        return view('pages.inventory.report', compact('spareparts', 'totalStock'));
    }
}