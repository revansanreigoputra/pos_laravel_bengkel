<?php

namespace App\Http\Controllers;

use App\Models\Customer; // Import model Customer
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Service;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Untuk manajemen storage
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Untuk memformat tanggal lebih baik

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Memuat relasi customer, item, service, dan sparepart untuk tampilan
        $transactions = Transaction::with(['customer', 'items.service', 'items.sparepart'])->latest()->get();

        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        return view('pages.transaction.index', compact('transactions', 'services', 'spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     * This method will be called when the user navigates to the /transactions/create URL.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        return view('pages.transaction.create', compact('services', 'spareparts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Incoming request for store transaction: ', $request->all());

        try {
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:255',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:0', // Changed to min:0 to allow 0 initially, client-side handles min:1 for actual sales
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'proof_of_transfer_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'status' => 'required|in:pending,completed,cancelled',
            ]);
            Log::info('Validation successful for store transaction.', $validatedData);
        } catch (ValidationException $e) {
            Log::error('Validation failed for store transaction: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Cari atau Buat Pelanggan Baru
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'] ?? null,
                    'address' => $validatedData['customer_address'] ?? null,
                ]
            );

            if ($customer->name !== $validatedData['customer_name']) {
                $customer->update(['name' => $validatedData['customer_name']]);
                Log::info('Customer name updated for existing customer ID: ' . $customer->id);
            }
            Log::info('Customer processed (ID: ' . $customer->id . ', Name: ' . $customer->name . ').');

            // 2. Validasi Stok Sparepart secara real-time dan hitung total harga
            $subTotalCalculated = 0;
            $errors = []; // Collect errors for ValidationException

            foreach ($validatedData['items'] as $index => $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                // Only process items with quantity > 0 for stock validation
                if ($quantity <= 0) {
                    // If quantity is 0, it means the item is effectively not being sold.
                    // We can skip stock validation for it, but still include it in subtotal if price is > 0.
                    $subTotalCalculated += ($price * $quantity);
                    continue; // Skip to next item
                }

                if ($type === 'sparepart') {
                    // Use lockForUpdate() to prevent race conditions
                    $sparepart = Sparepart::lockForUpdate()->find($id);

                    if (!$sparepart) {
                        $errors["items.{$index}.item_full_id"] = "Sparepart dengan ID {$id} tidak ditemukan.";
                        Log::error("Sparepart with ID {$id} not found during stock validation.");
                        continue; // Skip to next item
                    }

                    // Check if the requested quantity exceeds available stock
                    if ($sparepart->quantity < $quantity) {
                        $errors["items.{$index}.quantity"] = "Stok '{$sparepart->name}' tidak cukup. Tersedia: {$sparepart->quantity}, Diminta: {$quantity}.";
                        Log::warning("Insufficient stock for sparepart '{$sparepart->name}' (ID: {$id}). Available: {$sparepart->quantity}, Requested: {$quantity}.");
                    }
                }
                $subTotalCalculated += ($price * $quantity);
            }

            // If there are any stock errors, throw a ValidationException
            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }

            $globalDiscount = floatval($validatedData['global_discount'] ?? 0);
            $finalTotalCalculated = $subTotalCalculated - $globalDiscount;

            if ($finalTotalCalculated < 0) {
                $finalTotalCalculated = 0;
            }

            $frontendTotalPrice = floatval($validatedData['total_price']);
            if (abs($finalTotalCalculated - $frontendTotalPrice) > 0.01) {
                Log::warning("Frontend total_price ({$frontendTotalPrice}) differs from backend calculated total_price ({$finalTotalCalculated}). Using backend calculated total.");
                $finalTotalToSave = $finalTotalCalculated;
            } else {
                $finalTotalToSave = $frontendTotalPrice;
            }

            // 3. Upload Bukti Transfer jika ada
            $proofOfTransferUrl = null;
            if ($request->hasFile('proof_of_transfer_file')) {
                $file = $request->file('proof_of_transfer_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/proof_of_transfer', $fileName);
                $proofOfTransferUrl = 'storage/proof_of_transfer/' . $fileName;
                Log::info('Proof of transfer file uploaded: ' . $proofOfTransferUrl);
            }

            // 4. Buat Transaksi Baru
            $transaction = Transaction::create([
                'customer_id'           => $customer->id,
                'vehicle_number'        => $validatedData['vehicle_number'] ?? null,
                'transaction_date'      => $validatedData['transaction_date'],
                'discount_amount'       => $globalDiscount,
                'total_price'           => $finalTotalToSave,
                'invoice_number'        => $validatedData['invoice_number'],
                'vehicle_model'         => $validatedData['vehicle_model'] ?? null,
                'payment_method'        => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status'                => $validatedData['status'],
            ]);
            Log::info('Transaction created with ID: ' . $transaction->id);

            // 5. Tambahkan Item Transaksi dan Kurangi Stok
            foreach ($validatedData['items'] as $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                // Only create transaction item and decrement stock if quantity is positive
                if ($quantity > 0) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'item_type'      => $type,
                        'item_id'        => $id,
                        'price'          => $price,
                        'quantity'       => $quantity,
                    ]);
                    Log::info("Transaction item created for transaction {$transaction->id}: Type={$type}, ID={$id}, Quantity={$quantity}");

                    if ($type === 'sparepart') {
                        // Re-fetch with lockForUpdate() is not strictly needed here if already locked above
                        // but good practice if this loop could be outside the initial validation loop
                        $sparepart = Sparepart::find($id); // Already validated, just decrement
                        if ($sparepart) {
                            $sparepart->decrement('quantity', $quantity);
                            Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $id . ') quantity decremented by ' . $quantity . '. New quantity: ' . $sparepart->quantity);
                        }
                    }
                } else {
                    Log::info("Skipping transaction item creation and stock decrement for item ID {$id} due to zero or negative quantity.");
                }
            }

            DB::commit();
            Log::info('Transaction successfully committed.');
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during transaction creation (after initial validation): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing transaction: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     * This method is typically used for updating via a modal or a dedicated edit page.
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        Log::info('Incoming request for update transaction (ID: ' . $transaction->id . '): ', $request->all());

        try {
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'customer_address' => 'nullable|string|max:255',
                'vehicle_number' => 'nullable|string|max:255',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:0', // Changed to min:0
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $transaction->id,
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'proof_of_transfer_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'status' => 'required|in:pending,completed,cancelled',
                'clear_proof_of_transfer' => 'nullable|boolean',
            ]);
            Log::info('Validation successful for update transaction (ID: ' . $transaction->id . ').', $validatedData);
        } catch (ValidationException $e) {
            Log::error('Validation failed for update transaction (ID: ' . $transaction->id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. Cari atau Buat Pelanggan Baru untuk Update
            $customer = Customer::firstOrCreate(
                ['phone' => $validatedData['customer_phone']],
                [
                    'name' => $validatedData['customer_name'],
                    'email' => $validatedData['customer_email'] ?? null,
                    'address' => $validatedData['customer_address'] ?? null,
                ]
            );

            if ($customer->name !== $validatedData['customer_name']) {
                $customer->update(['name' => $validatedData['customer_name']]);
                Log::info('Customer name updated for existing customer ID: ' . $customer->id . ' during update.');
            }
            Log::info('Customer processed for update (ID: ' . $customer->id . ', Name: ' . $customer->name . ').');

            // Ambil kuantitas sparepart dari item transaksi lama sebelum dihapus
            // Ini penting untuk mengembalikan stok yang benar
            $oldSparepartQuantities = [];
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $oldSparepartQuantities[$oldItem->item_id] = ($oldSparepartQuantities[$oldItem->item_id] ?? 0) + $oldItem->quantity;
                    Log::info("Old sparepart quantity for ID {$oldItem->item_id}: {$oldItem->quantity}");
                }
            }

            // Validasi stok untuk item baru/yang diubah
            $subTotalCalculated = 0;
            $errors = [];
            $newSparepartQuantities = []; // To track quantities of spareparts in the new request

            foreach ($validatedData['items'] as $index => $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                // Only process items with quantity > 0 for stock validation
                if ($quantity <= 0) {
                    $subTotalCalculated += ($price * $quantity);
                    continue; // Skip to next item
                }

                if ($type === 'sparepart') {
                    // Track new quantities for later comparison
                    $newSparepartQuantities[$id] = ($newSparepartQuantities[$id] ?? 0) + $quantity;

                    // Get the old quantity for this specific sparepart from the original transaction
                    $oldQuantityForThisSparepart = $oldSparepartQuantities[$id] ?? 0;
                    $netChange = $newSparepartQuantities[$id] - $oldQuantityForThisSparepart;

                    // Fetch sparepart with lockForUpdate for accurate stock check
                    $sparepart = Sparepart::lockForUpdate()->find($id);

                    if (!$sparepart) {
                        $errors["items.{$index}.item_full_id"] = "Sparepart dengan ID {$id} tidak ditemukan.";
                        Log::error("Sparepart with ID {$id} not found during update stock validation.");
                        continue;
                    }

                    // If netChange is positive, it means we need to decrement stock
                    if ($netChange > 0) {
                        if ($sparepart->quantity < $netChange) {
                            $errors["items.{$index}.quantity"] = "Stok '{$sparepart->name}' tidak cukup untuk perubahan ini. Tersedia: {$sparepart->quantity}, Perlu dikurangi: {$netChange}.";
                            Log::warning("Insufficient stock for sparepart '{$sparepart->name}' (ID: {$id}) during update. Available: {$sparepart->quantity}, Net change: {$netChange}.");
                        }
                    }
                }
                $subTotalCalculated += ($price * $quantity);
            }

            // If there are any stock errors, throw a ValidationException
            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }


            $globalDiscount = floatval($validatedData['global_discount'] ?? 0);
            $finalTotalCalculated = $subTotalCalculated - $globalDiscount;
            if ($finalTotalCalculated < 0) {
                $finalTotalCalculated = 0;
            }

            $frontendTotalPrice = floatval($validatedData['total_price']);
            if (abs($finalTotalCalculated - $frontendTotalPrice) > 0.01) {
                Log::warning("Frontend total_price ({$frontendTotalPrice}) differs from backend calculated total_price ({$finalTotalCalculated}) during update. Using backend calculated total.");
                $finalTotalToSave = $finalTotalCalculated;
            } else {
                $finalTotalToSave = $frontendTotalPrice;
            }

            $proofOfTransferUrl = $transaction->proof_of_transfer_url;
            if ($request->hasFile('proof_of_transfer_file')) {
                // Hapus file lama jika ada
                if ($proofOfTransferUrl && Storage::disk('public')->exists(str_replace('storage/', '', $proofOfTransferUrl))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $proofOfTransferUrl));
                    Log::info('Old proof of transfer file deleted: ' . $proofOfTransferUrl);
                }
                $file = $request->file('proof_of_transfer_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/proof_of_transfer', $fileName);
                $proofOfTransferUrl = 'storage/proof_of_transfer/' . $fileName;
                Log::info('New proof of transfer file uploaded: ' . $proofOfTransferUrl);
            } elseif ($request->boolean('clear_proof_of_transfer')) {
                // Hapus file jika checkbox 'clear_proof_of_transfer' dicentang
                if ($proofOfTransferUrl && Storage::disk('public')->exists(str_replace('storage/', '', $proofOfTransferUrl))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $proofOfTransferUrl));
                    Log::info('Proof of transfer file cleared by user request: ' . $proofOfTransferUrl);
                }
                $proofOfTransferUrl = null;
            }

            $transaction->update([
                'customer_id'           => $customer->id,
                'vehicle_number'        => $validatedData['vehicle_number'] ?? null,
                'transaction_date'      => $validatedData['transaction_date'],
                'discount_amount'       => $globalDiscount,
                'total_price'           => $finalTotalToSave,
                'invoice_number'        => $validatedData['invoice_number'],
                'vehicle_model'         => $validatedData['vehicle_model'] ?? null,
                'payment_method'        => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status'                => $validatedData['status'],
            ]);
            Log::info('Transaction updated (ID: ' . $transaction->id . ').');

            // **STOCK ADJUSTMENT LOGIC - BEFORE DELETING OLD ITEMS**
            // Revert stock for all old items first
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($oldItem->item_id); // Find, no lock needed yet for reverting
                    if ($sparepart) {
                        $sparepart->increment('quantity', $oldItem->quantity);
                        Log::info("Reverted stock for sparepart {$sparepart->name} (ID: {$oldItem->item_id}) by {$oldItem->quantity}. New stock: {$sparepart->quantity}");
                    }
                }
            }

            // Hapus semua item transaksi lama
            $transaction->items()->delete();
            Log::info('Old transaction items deleted for transaction ID: ' . $transaction->id);

            // Tambahkan item transaksi yang diperbarui dan kurangi stok baru
            foreach ($validatedData['items'] as $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                // Only create transaction item and decrement stock if quantity is positive
                if ($quantity > 0) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'item_type'      => $type,
                        'item_id'        => $id,
                        'price'          => $price,
                        'quantity'       => $quantity,
                    ]);
                    Log::info("New transaction item created for transaction {$transaction->id}: Type={$type}, ID={$id}, Quantity={$quantity}");

                    if ($type === 'sparepart') {
                        $sparepart = Sparepart::find($id); // Already validated and potentially locked earlier in this request
                        if ($sparepart) {
                            $sparepart->decrement('quantity', $quantity);
                            Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $id . ') quantity decremented by ' . $quantity . '. New stock: ' . $sparepart->quantity);
                        }
                    }
                } else {
                    Log::info("Skipping new transaction item creation and stock decrement for item ID {$id} due to zero or negative quantity.");
                }
            }

            DB::commit();
            Log::info('Transaction update successfully committed (ID: ' . $transaction->id . ').');
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed during transaction update (after initial validation): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating transaction (ID: ' . $transaction->id . '): ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'Gagal mengedit transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Transaction $transaction)
    {
        // Pastikan relasi customer dimuat saat mengedit
        $transaction->load(['customer', 'items.service', 'items.sparepart']);

        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        return view('pages.transaction.edit', compact('transaction', 'services', 'spareparts'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            Log::info('Attempting to delete transaction (ID: ' . $transaction->id . ').');
            // Kembalikan stok sparepart sebelum menghapus item dan transaksi
            foreach ($transaction->items as $item) {
                if ($item->item_type === 'sparepart') {
                    // Use lockForUpdate() here as well to prevent race conditions during stock return
                    $sparepart = Sparepart::lockForUpdate()->find($item->item_id);
                    if ($sparepart) {
                        $sparepart->increment('quantity', $item->quantity);
                        Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $item->item_id . ') stock returned by ' . $item->quantity . ' due to transaction deletion. New stock: ' . $sparepart->quantity);
                    } else {
                        Log::warning('Sparepart dengan ID ' . $item->item_id . ' tidak ditemukan saat mengembalikan stok untuk transaksi yang akan dihapus. Ini mungkin indikasi data tidak konsisten.');
                    }
                }
            }

            // Hapus file bukti transfer jika ada
            if ($transaction->proof_of_transfer_url && Storage::disk('public')->exists(str_replace('storage/', '', $transaction->proof_of_transfer_url))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $transaction->proof_of_transfer_url));
                Log::info('Proof of transfer file deleted for transaction ID: ' . $transaction->id);
            }

            $transaction->items()->delete(); // Hapus item-item terkait
            $transaction->delete(); // Hapus transaksi itu sendiri

            DB::commit(); // Commit the transaction
            Log::info('Transaction (ID: ' . $transaction->id . ') successfully deleted.');
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback on error
            Log::error('Error deleting transaction (ID: ' . $transaction->id . '): ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
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

        $pdf = Pdf::loadView('pages.transaction.invoice_pdf', $data);
        return $pdf->download('invoice-' . $transaction->invoice_number . '.pdf');
    }
}
