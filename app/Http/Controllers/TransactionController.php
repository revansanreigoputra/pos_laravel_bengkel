<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Sparepart; // Pastikan Sparepart diimpor
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Services\TransactionService;
use Exception;
use Illuminate\Validation\ValidationException; // Impor ini untuk menangkap ValidationException

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
                'status' => 'required|string|in:pending,completed,cancelled',
            ]);

            Log::info('Validation passed', $validatedData);

            // 2. Pendaftaran Pelanggan Otomatis
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'],
                    'address' => $validatedData['customer_address']
                ]
            );

            Log::info('Customer resolved or created', ['customer_id' => $customer->id]);

            // Persiapkan data transaksi
            $transactionData = [
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'] ?? null,
                'vehicle_model' => $validatedData['vehicle_model'] ?? null,
                'invoice_number' => $validatedData['invoice_number'],
                'payment_method' => $validatedData['payment_method'],
                'transaction_date' => $validatedData['transaction_date'],
                'status' => $validatedData['status'],
                'total_price' => 0,
            ];

            $itemsData = [];

            foreach ($validatedData['items'] as $index => $item) {
                list($itemType, $itemId) = explode('-', $item['item_full_id']);

                Log::info("Processing item", [
                    'index' => $index,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity' => $item['quantity']
                ]);

                if ($itemType === 'sparepart') {
                    $sparepart = Sparepart::with('purchaseOrderItems')->find($itemId);

                    if (!$sparepart) {
                        throw ValidationException::withMessages([
                            "items.$index.item_full_id" => "Sparepart dengan ID $itemId tidak ditemukan."
                        ]);
                    }

                    if ($sparepart->available_stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            "items.$index.quantity" =>
                            "Stok sparepart '{$sparepart->name}' tidak cukup. Tersedia: {$sparepart->available_stock}."
                        ]);
                    }
                }

                $itemsData[] = [
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                    'quantity' => $item['quantity'],
                ];
            }

            Log::info('All items processed, creating transaction', [
                'transaction_data' => $transactionData,
                'items_data' => $itemsData
            ]);

            // Panggil service untuk membuat transaksi
            $transaction = $this->transactionService->createTransaction($transactionData, $itemsData);

            Log::info('Transaction successfully created', ['transaction_id' => $transaction->id]);

            if ($transaction->status === 'completed') {
                return redirect()->route('transaction.index')
                    ->with('success', 'Transaksi berhasil dibuat!')
                    ->with('print_invoice', $transaction->id);
            } else {
                return redirect()->route('transaction.index')
                    ->with('success', 'Transaksi berhasil dibuat!');
            }
        } catch (ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Unexpected error while storing transaction', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Perbarui transaksi yang ada.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'invoice_number' => 'required|string|unique:transactions,invoice_number,' . $id,
                'transaction_date' => 'required|date',
                'customer_name' => 'required|string',
                'customer_phone' => 'required|string',
                'customer_email' => 'nullable|email',
                'customer_address' => 'nullable|string',
                'vehicle_number' => 'nullable|string',
                'vehicle_model' => 'nullable|string',
                'payment_method' => 'required|string',
                'status' => 'required|string|in:pending,completed,canceled',
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.item_type' => 'required|string|in:service,sparepart',
                'items.*.item_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.id' => 'nullable|exists:transaction_items,id'
            ]);

            $transaction = Transaction::with('items')->findOrFail($id);

            // Update data customer
            $customer = Customer::updateOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'],
                    'address' => $validatedData['customer_address']
                ]
            );

            // Simpan item lama untuk penyesuaian stok
            $oldItems = $transaction->items->keyBy('id');

            // Update data transaksi utama
            $transaction->update([
                'invoice_number' => $validatedData['invoice_number'],
                'transaction_date' => $validatedData['transaction_date'],
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'],
                'vehicle_model' => $validatedData['vehicle_model'],
                'payment_method' => $validatedData['payment_method'],
                'status' => $validatedData['status'],
                'discount_amount' => $validatedData['global_discount'] ?? 0,
                'total_price' => $validatedData['total_price']
            ]);

            // Proses item-item transaksi
            $existingItemIds = [];

            foreach ($validatedData['items'] as $itemData) {
                $itemType = $itemData['item_type'];
                $itemId = $itemData['item_id'];
                $quantity = $itemData['quantity'];

                if (isset($itemData['id'])) {
                    // Update item yang sudah ada
                    $item = $oldItems->get($itemData['id']);

                    if ($item) {
                        // Kembalikan stok jika item adalah sparepart dan quantity berubah
                        if ($item->item_type === 'sparepart' && $item->quantity != $quantity) {
                            $this->adjustSparepartStock($item->item_id, $item->quantity, false); // Kembalikan stok lama
                            $this->adjustSparepartStock($itemId, $quantity, true); // Kurangi stok baru
                        }

                        $item->update([
                            'item_type' => $itemType,
                            'item_id' => $itemId,
                            'quantity' => $quantity,
                            'price' => $itemData['price']
                        ]);
                        $existingItemIds[] = $item->id;
                    }
                } else {
                    // Tambahkan item baru
                    $newItem = $transaction->items()->create([
                        'item_type' => $itemType,
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                        'price' => $itemData['price']
                    ]);
                    $existingItemIds[] = $newItem->id;

                    // Kurangi stok jika item adalah sparepart
                    if ($itemType === 'sparepart') {
                        $this->adjustSparepartStock($itemId, $quantity, true);
                    }
                }
            }

            // Hapus item yang tidak ada dalam request dan kembalikan stok jika perlu
            $itemsToDelete = $transaction->items()->whereNotIn('id', $existingItemIds)->get();

            foreach ($itemsToDelete as $item) {
                if ($item->item_type === 'sparepart') {
                    $this->adjustSparepartStock($item->item_id, $item->quantity, false);
                }
                $item->delete();
            }

            DB::commit();

            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    // Helper function untuk menyesuaikan stok sparepart
    private function adjustSparepartStock($sparepartId, $quantity, $isSold)
    {
        $sparepart = Sparepart::find($sparepartId);
        if (!$sparepart) return;

        if ($isSold) {
            // Kurangi stok sparepart
            $sparepart->decrement('available_stock', $quantity);

            // Tambahkan sold_quantity ke batch (purchase_order_items) FEFO/FIFO
            $qty = $quantity;
            $batches = $sparepart->purchaseOrderItems()
                ->whereRaw('quantity - sold_quantity > 0')
                ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date ASC, created_at ASC')
                ->get();

            foreach ($batches as $batch) {
                $available = $batch->quantity - $batch->sold_quantity;
                $take = min($available, $qty);
                $batch->increment('sold_quantity', $take);
                $qty -= $take;
                if ($qty <= 0) break;
            }
        } else {
            // Kembalikan stok sparepart
            $sparepart->increment('available_stock', $quantity);

            // Kembalikan sold_quantity ke batch (purchase_order_items) FEFO/FIFO reverse
            $qty = $quantity;
            $batches = $sparepart->purchaseOrderItems()
                ->where('sold_quantity', '>', 0)
                ->orderByRaw('CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END, expired_date DESC, created_at DESC')
                ->get();

            foreach ($batches as $batch) {
                $sold = $batch->sold_quantity;
                $take = min($sold, $qty);
                $batch->decrement('sold_quantity', $take);
                $qty -= $take;
                if ($qty <= 0) break;
            }
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
        // Eager load purchaseOrderItems untuk available_stock
        $spareparts = Sparepart::with('purchaseOrderItems')->get();
        $services = Service::where('status', 'aktif')->get();
        $customer2 = Customer::all(['name', 'phone', 'email', 'address']);
        return view('pages.transaction.create', compact('spareparts', 'services', 'customer2'));
    }

    public function index()
    {
        $transactions = Transaction::with(['customer', 'items.sparepart', 'items.service'])->latest()->get();
        return view('pages.transaction.index', compact('transactions'));
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load('items.sparepart.purchaseOrderItems', 'items.service'); // Eager load untuk edit
        $spareparts = Sparepart::with('purchaseOrderItems')->get();
        $services = Service::where('status', 'aktif')->get();
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
    public function getLatestTransactions(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $transactions = Transaction::with(['customer', 'items.sparepart', 'items.service'])
            ->latest()
            ->paginate($perPage);

        return response()->json($transactions);
    }
}
