<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\PurchaseOrderItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class TransactionController extends Controller
{
    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        Log::info('Incoming request for new transaction: ', $request->all());

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

            // 3. Proses Item Transaksi (Validasi, Perhitungan Harga, Pengecekan Stok)
            $processedItems = $this->processTransactionItems($validatedData['items']);

            // 4. Hitung total harga dan diskon
            $totalPrice = array_sum(array_column($processedItems, 'subtotal'));
            $totalDiscountAmount = array_sum(array_column($processedItems, 'discount_amount'));
            $globalDiscount = $request->input('global_discount', 0);
            $totalDiscountAmount += $globalDiscount;
            $finalTotalPrice = $totalPrice - $globalDiscount;
            if ($finalTotalPrice < 0) $finalTotalPrice = 0;

            // 5. Buat Transaksi
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'] ?? null,
                'vehicle_model' => $validatedData['vehicle_model'] ?? null,
                'invoice_number' => $validatedData['invoice_number'],
                'payment_method' => $validatedData['payment_method'],
                'total_price' => $finalTotalPrice,
                'discount_amount' => $totalDiscountAmount,
                'transaction_date' => $validatedData['transaction_date'],
                'status' => $request->input('status', 'pending'),
            ]);

            // 6. Kurangi Stok dan Simpan Item Transaksi
            foreach ($processedItems as $itemData) {
                if ($itemData['item_type'] === 'sparepart') {
                    Sparepart::find($itemData['item_id'])->decrement('stock', $itemData['quantity']);
                }
                
                $transaction->items()->create([
                    'item_type' => $itemData['item_type'],
                    'item_id' => $itemData['item_id'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error storing transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $spareparts = Sparepart::all();
        $services = Service::all();

        return view('pages.transaction.create', compact('spareparts', 'services'));
    }

    /**
     * Tampilkan daftar transaksi.
     */
    public function index()
    {
        $transactions = Transaction::with('customer', 'items.sparepart', 'items.service')->latest()->paginate(10);

        return view('pages.transaction.index', compact('transactions'));
    }

    /**
     * Tampilkan form untuk mengedit transaksi.
     */
    public function edit(Transaction $transaction)
    {
        $spareparts = Sparepart::all();
        $services = Service::all();
        
        return view('pages.transaction.edit', compact('transaction', 'spareparts', 'services'));
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
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($oldItem->item_id);
                    if ($sparepart) {
                        $sparepart->increment('stock', $oldItem->quantity);
                    }
                }
            }

            // 3. Update data pelanggan atau buat baru
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'],
                    'address' => $validatedData['customer_address']
                ]
            );

            // 4. Proses Item Transaksi (Validasi, Perhitungan Harga, Pengecekan Stok)
            $processedItems = $this->processTransactionItems($validatedData['items']);

            // 5. Hitung ulang total harga dan diskon
            $totalPrice = array_sum(array_column($processedItems, 'subtotal'));
            $totalDiscountAmount = array_sum(array_column($processedItems, 'discount_amount'));
            $globalDiscount = $request->input('global_discount', 0);
            $totalDiscountAmount += $globalDiscount;
            $finalTotalPrice = $totalPrice - $globalDiscount;
            if ($finalTotalPrice < 0) $finalTotalPrice = 0;

            // 6. Update Transaksi
            $transaction->update([
                'customer_id' => $customer->id,
                'vehicle_number' => $validatedData['vehicle_number'] ?? null,
                'vehicle_model' => $validatedData['vehicle_model'] ?? null,
                'invoice_number' => $validatedData['invoice_number'],
                'payment_method' => $validatedData['payment_method'],
                'total_price' => $finalTotalPrice,
                'discount_amount' => $totalDiscountAmount,
                'transaction_date' => $validatedData['transaction_date'],
                'status' => $request->input('status', 'pending'),
            ]);

            // 7. Hapus item lama dan buat item baru
            $transaction->items()->delete();
            foreach ($processedItems as $itemData) {
                if ($itemData['item_type'] === 'sparepart') {
                    Sparepart::find($itemData['item_id'])->decrement('stock', $itemData['quantity']);
                }
                
                $transaction->items()->create([
                    'item_type' => $itemData['item_type'],
                    'item_id' => $itemData['item_id'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                ]);
            }

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
            foreach ($transaction->items as $item) {
                if ($item->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($item->item_id);
                    if ($sparepart) {
                        $sparepart->increment('stock', $item->quantity);
                    }
                }
            }
            $transaction->delete();
            DB::commit();
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error deleting transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Metode privat untuk memproses item transaksi.
     * Melakukan validasi, pengecekan stok/kadaluarsa, dan menghitung harga.
     * @param array $items
     * @return array
     * @throws \Exception
     */
    protected function processTransactionItems(array $items): array
    {
        $processedItems = [];
        foreach ($items as $itemData) {
            list($itemType, $itemId) = explode('-', $itemData['item_full_id']);
            $price = 0;
            $discountAmount = 0;

            if ($itemType === 'sparepart') {
                $sparepart = Sparepart::find($itemId);

                if (!$sparepart) {
                    throw new \Exception("Sparepart dengan ID " . $itemId . " tidak ditemukan.");
                }

                if ($sparepart->available_stock < $itemData['quantity']) {
                    throw new \Exception("Stok sparepart '{$sparepart->name}' tidak mencukupi. Stok tersedia: {$sparepart->available_stock}");
                }

                $expiredItems = PurchaseOrderItem::where('sparepart_id', $sparepart->id)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('expired_date')
                    ->where('expired_date', '<', Carbon::today())
                    ->orderBy('expired_date', 'asc')
                    ->get();

                if ($expiredItems->isNotEmpty()) {
                    throw new \Exception("Sparepart '{$sparepart->name}' memiliki stok yang sudah kadaluarsa. Mohon periksa kembali.");
                }

                $price = $sparepart->final_selling_price;
                if ($sparepart->isDiscountActive()) {
                    $originalPrice = $sparepart->selling_price;
                    $discountPerItem = $originalPrice - $price;
                    $discountAmount = ($discountPerItem * $itemData['quantity']);
                }

            } elseif ($itemType === 'service') {
                $service = Service::find($itemId);

                if (!$service) {
                    throw new \Exception("Jasa service dengan ID " . $itemId . " tidak ditemukan.");
                }
                $price = $service->harga_standar;
            } else {
                throw new \Exception("Tipe item tidak valid: " . $itemType);
            }

            $processedItems[] = [
                'item_type' => $itemType,
                'item_id' => $itemId,
                'price' => $price,
                'quantity' => $itemData['quantity'],
                'subtotal' => $price * $itemData['quantity'],
                'discount_amount' => $discountAmount,
            ];
        }

        return $processedItems;
    }

    /**
     * Tampilkan detail transaksi.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'items.sparepart', 'items.service');
        return view('pages.transaction.show', compact('transaction'));
    }

    /**
     * Export transactions to PDF (invoice).
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Transaction $transaction)
    {
        // Pastikan relasi customer dimuat saat membuat PDF
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