<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\PurchaseOrder;
use App\Models\Transaction;
use App\Models\Sparepart;
use App\Models\PurchaseOrderItem;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\SparepartLogExport;
use Maatwebsite\Excel\Facades\Excel;
class LogController extends Controller
{
    public function logPembelian(Request $request)
    {
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Start a query on the PurchaseOrder model with eager loading
        $query = PurchaseOrder::with(['supplier', 'items.sparepart'])->latest();

        // Apply status filter if it exists
        if ($status) {
            $query->where('status', $status);
        }

        // Apply date range filter if both start and end dates are provided
        if ($startDate && $endDate) {
            $query->whereBetween('order_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        // Paginate the results
        $purchaseOrders = $query->paginate(10);

        // Pass the filter values back to the view to maintain form state
        return view('logs.pembelian', compact('purchaseOrders', 'status', 'startDate', 'endDate'));
    }
    public function logPenjualan()
    {
        $transactions = Transaction::with(['customer', 'items.sparepart', 'items.service'])->latest()->paginate(10);
        return view('logs.penjualan', compact('transactions'));
    }

    public function logPergerakanStok(Request $request)
    {
        $spareparts = Sparepart::with(['purchaseOrderItems', 'transactionItems'])->paginate(10);
        return view('logs.stok', compact('spareparts'));
    }

    public function logSparepart(Request $request)
    {
        $tipe = $request->input('tipe', 'stok_saat_ini');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($tipe == 'stok_saat_ini') {
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now();

            $spareparts = Sparepart::with(['category', 'purchaseOrderItems'])->get();
            $data = $spareparts->map(function ($item, $idx) use ($endDate) {
                // Ambil semua batch (purchaseOrderItems) yang masuk sebelum/tanggal endDate
                $validItems = $item->purchaseOrderItems()
                    ->whereHas('purchaseOrder', function ($q) use ($endDate) {
                        $q->where('order_date', '<=', $endDate);
                    })
                    ->get();

                // Hitung total masuk dan total keluar dari batch valid
                $totalMasuk = $validItems->sum('quantity');
                $totalKeluar = $validItems->sum('sold_quantity');

                return [
                    'no' => $idx + 1,
                    'nama_sparepart' => $item->name,
                    'kategori' => $item->category->name ?? '-',
                    'stok_tersedia' => $totalMasuk - $totalKeluar,
                ];
            });
        } elseif ($tipe == 'stok_masuk') {
            // Stok masuk
            $query = \App\Models\PurchaseOrderItem::with(['sparepart.category', 'purchaseOrder']);
            if ($startDate && $endDate) {
                $query->whereHas('purchaseOrder', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_date', [
                        \Carbon\Carbon::parse($startDate)->startOfDay(),
                        \Carbon\Carbon::parse($endDate)->endOfDay()
                    ]);
                });
            }
            $barangMasuk = $query->get()->map(function ($item, $idx) {
                return [
                    'no' => $idx + 1,
                    'tanggal' => $item->purchaseOrder->order_date ? $item->purchaseOrder->order_date->format('Y-m-d') : '-',
                    'nama_sparepart' => $item->sparepart->name ?? '-',
                    'kategori' => $item->sparepart->category->name ?? '-',
                    'jumlah_masuk' => $item->quantity,
                ];
            });
            $data = $barangMasuk;
        } elseif ($tipe == 'stok_keluar') {
            // Stok keluar
            $query = \App\Models\TransactionItem::with(['sparepart.category', 'transaction'])->where('item_type', 'sparepart');
            if ($startDate && $endDate) {
                $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [
                        \Carbon\Carbon::parse($startDate)->startOfDay(),
                        \Carbon\Carbon::parse($endDate)->endOfDay()
                    ]);
                });
            }
            $barangKeluar = $query->get()->map(function ($item, $idx) {
                return [
                    'no' => $idx + 1,
                    'tanggal' => $item->transaction->transaction_date ? $item->transaction->transaction_date->format('Y-m-d') : '-',
                    'nama_sparepart' => $item->sparepart->name ?? '-',
                    'kategori' => $item->sparepart->category->name ?? '-',
                    'jumlah_keluar' => $item->quantity,
                ];
            });
            $data = $barangKeluar;
        } else {
            $data = collect();
        }

        return view('logs.sparepart', compact('data', 'tipe'));
    }

    public function logSparepartDetail(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        // Barang Masuk (Purchase)
        $barangMasuk = \App\Models\PurchaseOrderItem::with(['purchaseOrder.supplier', 'sparepart'])
            ->whereHas('purchaseOrder', fn($q) => $q->whereBetween('order_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->purchaseOrder->invoice_number ?? '-',
                    'supplier' => $item->purchaseOrder->supplier->name ?? '-',
                    'customer' => null,
                    'jenis' => 'Barang Masuk',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Barang Keluar (Transaction)
        $barangKeluar = \App\Models\TransactionItem::with(['transaction.customer', 'sparepart'])
            ->where('item_type', 'sparepart')
            ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]))
            ->get()
            ->map(function ($item) {
                return [
                    'no_invoice' => $item->transaction->invoice_number ?? '-',
                    'supplier' => null,
                    'customer' => $item->transaction->customer->name ?? '-',
                    'jenis' => 'Barang Keluar',
                    'sparepart' => $item->sparepart->name ?? '-',
                    'quantity' => $item->quantity,
                ];
            });

        // Gabungkan dan urutkan
        $data = $barangMasuk->concat($barangKeluar)->values();

        return view('logs.sparepart_detail', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    // cetak pdf function

    /**
     * Export purchase log data to PDF based on filters.
     */
    public function exportPdfPembelian(Request $request)
    {
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = PurchaseOrder::with(['supplier', 'items.sparepart'])->latest();

        // Apply filters (same logic as in the main function)
        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('order_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $purchaseOrders = $query->get(); // Get all records, not just one page

        $pdf = PDF::loadView('logs.pdf.pembelian-pdf', compact('purchaseOrders', 'status', 'startDate', 'endDate'));

        $fileName = 'Laporan_Pembelian_' . Carbon::now()->format('d F Y') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export sales log data to PDF based on filters.
     */
    public function exportPdfPenjualan(Request $request, PDF $pdf)
    {
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Transaction::with(['customer', 'items.sparepart', 'items.service'])->latest();
 

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $transactions = $query->get(); // Get all records, not just one page

        // Load the data into the PDF view
          $pdf = Pdf::loadView('logs.pdf.penjualan-pdf', compact('transactions', 'status', 'startDate', 'endDate'));

    $fileName = 'Laporan_Penjualan_' . Carbon::now()->format('d F Y') . '.pdf';

    return $pdf->download($fileName);
    }
     // EXCEL EXPORT RIWAYAT STOK
     public function exportExcelLogSparepart(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // The `$tipe` variable is no longer needed to control which sheet is exported,
        // but it can still be useful for the filename.
        $tipe = $request->get('tipe', 'Semua');

        $fileName = 'Laporan_Stok_' . $tipe . '_' . Carbon::now()->format('d F Y') . '.xlsx';

        // The export class will now always produce all three sheets,
        // but the data in each sheet will still be filtered by the date range.
        return Excel::download(new SparepartLogExport($tipe, $startDate, $endDate), $fileName);
    }
}
