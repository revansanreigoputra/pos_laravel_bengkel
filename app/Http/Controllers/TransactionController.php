<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Sparepart;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Services\TransactionService;
use Exception;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    // Injeksi TransactionService melalui constructor
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        Log::info('Incoming request for new transaction: ', $request->all());

        try {
            // 1. Validasi Data
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:255',
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            // 2. Pendaftaran Pelanggan Otomatis
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'],
                    'address' => $validatedData['customer_address']
                ]
            );

            // Persiapkan data untuk service
            $transactionData = [
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'] ?? null,
                'vehicle_model' => $validatedData['vehicle_model'] ?? null,
                'invoice_number' => $validatedData['invoice_number'],
                'payment_method' => $validatedData['payment_method'],
                'transaction_date' => $validatedData['transaction_date'],
                'status' => $request->input('status', 'pending'),
                'total_price' => 0, // Akan di-update oleh service
            ];

            // Ubah format item agar service bisa memprosesnya
            $itemsData = [];
            foreach ($validatedData['items'] as $item) {
                list($itemType, $itemId) = explode('-', $item['item_full_id']);
                $itemsData[] = [
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity' => $item['quantity'],
                ];
            }

            // Panggil service untuk membuat transaksi dan memproses stok
            $transaction = $this->transactionService->createTransaction($transactionData, $itemsData);

            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dibuat!');
        } catch (Exception $e) {
            Log::error('Error storing transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Perbarui transaksi yang ada.
     */
    public function update(Request $request, Transaction $transaction)
    {
        DB::beginTransaction();

        try {
            // 1. Validasi Data
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:255',
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $transaction->id,
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            // 2. Kembalikan stok lama sebelum di-update
            $this->transactionService->restoreStockFromTransaction($transaction);
            
            // 3. Hapus item lama
            $transaction->items()->delete();
            
            // 4. Update data pelanggan atau buat baru
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'],
                    'address' => $validatedData['customer_address']
                ]
            );

            // Persiapkan data untuk service
            $transactionData = [
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'] ?? null,
                'vehicle_model' => $validatedData['vehicle_model'] ?? null,
                'invoice_number' => $validatedData['invoice_number'],
                'payment_method' => $validatedData['payment_method'],
                'transaction_date' => $validatedData['transaction_date'],
                'status' => $request->input('status', 'pending'),
                'total_price' => 0, // Akan di-update oleh service
            ];

            $itemsData = [];
            foreach ($validatedData['items'] as $item) {
                list($itemType, $itemId) = explode('-', $item['item_full_id']);
                $itemsData[] = [
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity' => $item['quantity'],
                ];
            }

            // 5. Buat ulang item transaksi dengan logika stok yang baru
            $updatedTransaction = $this->transactionService->createTransaction($transactionData, $itemsData);

            // 6. Update transaksi utama dengan data dari service
            $transaction->update($updatedTransaction->toArray());

            DB::commit();
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus transaksi dari database.
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Kembalikan stok sparepart sebelum menghapus transaksi
            $this->transactionService->restoreStockFromTransaction($transaction);
            
            $transaction->delete();
            DB::commit();
            
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    // Metode lain tidak mengalami perubahan signifikan dan tetap berada di sini
    public function create()
    {
        $spareparts = Sparepart::all();
        $services = Service::all();
        return view('pages.transaction.create', compact('spareparts', 'services'));
    }

    public function index()
    {
        $transactions = Transaction::with('customer', 'items.sparepart', 'items.service')->latest()->paginate(10);
        return view('pages.transaction.index', compact('transactions'));
    }

    public function edit(Transaction $transaction)
    {
        $spareparts = Sparepart::all();
        $services = Service::all();
        return view('pages.transaction.edit', compact('transaction', 'spareparts', 'services'));
    }
    
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'items.sparepart', 'items.service');
        return view('pages.transaction.show', compact('transaction'));
    }
    
    public function exportPdf(Transaction $transaction)
    {
        $transaction->load(['customer', 'items.service', 'items.sparepart']);
        $data = [
            'transaction' => $transaction,
            'nama_bengkel' => 'BengkelKu',
            'alamat_bengkel' => 'Jl. Contoh No. 123, Godean, Yogyakarta',
            'telepon_bengkel' => '0812-3456-7890',
            'tanggal_cetak' => Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss'),
        ];

        $pdf = PDF::loadView('pages.transaction.invoice_pdf', $data);
        return $pdf->download('invoice-' . $transaction->invoice_number . '.pdf');
    }
}