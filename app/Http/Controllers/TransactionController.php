<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Service;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk manajemen storage
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
        // Memuat relasi item, service, dan sparepart untuk tampilan
        $transactions = Transaction::with(['items.service', 'items.sparepart'])->latest()->get();

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
        // Log request data untuk debugging awal
        Log::info('Incoming request for store transaction: ', $request->all());

        try {
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:255',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
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
            $subTotalCalculated = 0;
            foreach ($validatedData['items'] as $itemData) {
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);
                $subTotalCalculated += ($price * $quantity);
            }
            $globalDiscount = floatval($validatedData['global_discount'] ?? 0); // Pastikan mengambil dari validatedData atau default 0
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

            $proofOfTransferUrl = null;
            if ($request->hasFile('proof_of_transfer_file')) {
                $file = $request->file('proof_of_transfer_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                // Simpan di storage/app/public/proof_of_transfer
                $file->storeAs('public/proof_of_transfer', $fileName);
                // Path yang bisa diakses publik melalui php artisan storage:link
                $proofOfTransferUrl = 'storage/proof_of_transfer/' . $fileName;
                Log::info('Proof of transfer file uploaded: ' . $proofOfTransferUrl);
            }

            $transaction = Transaction::create([
                'customer_name'        => $validatedData['customer_name'],
                'vehicle_number'       => $validatedData['vehicle_number'],
                'transaction_date'     => $validatedData['transaction_date'],
                'discount_amount'      => $globalDiscount,
                'total_price'          => $finalTotalToSave,
                'invoice_number'       => $validatedData['invoice_number'],
                'vehicle_model'        => $validatedData['vehicle_model'],
                'payment_method'       => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status'               => $validatedData['status'],
            ]);
            Log::info('Transaction created with ID: ' . $transaction->id);

            foreach ($validatedData['items'] as $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                Log::info("Processing item: Type={$type}, ID={$id}, Price={$price}, Quantity={$quantity}");

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $type,
                    'item_id'        => $id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                ]);
                Log::info("Transaction item created for transaction {$transaction->id}: Type={$type}, ID={$id}");


                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        Log::info("Sparepart found (ID: {$id}). Current quantity: {$sparepart->quantity}, Requested quantity: {$quantity}");
                        if ($sparepart->quantity >= $quantity) {
                            $sparepart->decrement('quantity', $quantity);
                            Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $id . ') quantity decremented by ' . $quantity . '. New quantity: ' . $sparepart->quantity);
                        } else {
                            Log::warning('Stock sparepart ' . $sparepart->name . ' (ID: ' . $id . ') tidak mencukupi. Sisa stok: ' . $sparepart->quantity . ', Permintaan: ' . $quantity);
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi. Sisa stok: ' . $sparepart->quantity)->withInput();
                        }
                    } else {
                        Log::error('Sparepart dengan ID ' . $id . ' tidak ditemukan saat mencoba mengurangi stok.');
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan.')->withInput();
                    }
                }
            }

            DB::commit();
            Log::info('Transaction successfully committed.');
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil ditambahkan!');
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
                'vehicle_number' => 'required|string|max:255',
                'transaction_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $transaction->id,
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'proof_of_transfer_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'status' => 'required|in:pending,completed,cancelled',
                'clear_proof_of_transfer' => 'nullable|boolean', // Untuk menghapus file bukti transfer
            ]);
            Log::info('Validation successful for update transaction (ID: ' . $transaction->id . ').', $validatedData);
        } catch (ValidationException $e) {
            Log::error('Validation failed for update transaction (ID: ' . $transaction->id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $oldSparepartQuantities = [];
            // Ambil kuantitas sparepart dari item transaksi lama sebelum dihapus
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $oldSparepartQuantities[$oldItem->item_id] = ($oldSparepartQuantities[$oldItem->item_id] ?? 0) + $oldItem->quantity;
                    Log::info("Old sparepart quantity for ID {$oldItem->item_id}: {$oldItem->quantity}");
                }
            }

            $subTotalCalculated = 0;
            foreach ($validatedData['items'] as $itemData) {
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);
                $subTotalCalculated += ($price * $quantity);
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
                'customer_name'         => $validatedData['customer_name'],
                'vehicle_number'        => $validatedData['vehicle_number'],
                'transaction_date'      => $validatedData['transaction_date'],
                'discount_amount'       => $globalDiscount,
                'total_price'           => $finalTotalToSave,
                'invoice_number'        => $validatedData['invoice_number'],
                'vehicle_model'         => $validatedData['vehicle_model'],
                'payment_method'        => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status'                => $validatedData['status'],
            ]);
            Log::info('Transaction updated (ID: ' . $transaction->id . ').');

            // Hapus semua item transaksi lama
            $transaction->items()->delete();
            Log::info('Old transaction items deleted for transaction ID: ' . $transaction->id);


            foreach ($validatedData['items'] as $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                Log::info("Processing new/updated item: Type={$type}, ID={$id}, Price={$price}, Quantity={$quantity}");

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $type,
                    'item_id'        => $id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                ]);
                Log::info("New transaction item created for transaction {$transaction->id}: Type={$type}, ID={$id}");

                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        $oldQuantityForThisSparepart = $oldSparepartQuantities[$id] ?? 0;
                        $netChange = $quantity - $oldQuantityForThisSparepart; // Perubahan bersih kuantitas

                        Log::info("Sparepart (ID: {$id}) - Old quantity in transaction: {$oldQuantityForThisSparepart}, New quantity in transaction: {$quantity}, Net change: {$netChange}. Current stock: {$sparepart->quantity}");

                        if ($netChange > 0) { // Jika kuantitas baru lebih besar, berarti stok harus berkurang
                            if ($sparepart->quantity >= $netChange) {
                                $sparepart->decrement('quantity', $netChange);
                                Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $id . ') quantity decremented by ' . $netChange . '. New stock: ' . $sparepart->quantity);
                            } else {
                                Log::warning('Stok sparepart ' . $sparepart->name . ' (ID: ' . $id . ') tidak mencukupi untuk perubahan. Sisa stok: ' . $sparepart->quantity . ', Perlu dikurangi: ' . $netChange);
                                DB::rollBack();
                                return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi untuk perubahan ini. Sisa stok: ' . $sparepart->quantity)->withInput();
                            }
                        } elseif ($netChange < 0) { // Jika kuantitas baru lebih kecil, berarti stok harus bertambah
                            $sparepart->increment('quantity', abs($netChange));
                            Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $id . ') quantity incremented by ' . abs($netChange) . '. New stock: ' . $sparepart->quantity);
                        }
                        // Hapus ID sparepart dari array lama karena sudah diproses
                        unset($oldSparepartQuantities[$id]);
                    } else {
                        Log::error('Sparepart dengan ID ' . $id . ' tidak ditemukan saat update item transaksi.');
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan saat update.')->withInput();
                    }
                }
            }

            // Kembalikan stok untuk sparepart yang dihapus sepenuhnya dari transaksi
            foreach ($oldSparepartQuantities as $sparepartId => $qtyToReturn) {
                $sparepart = Sparepart::find($sparepartId);
                if ($sparepart) {
                    $sparepart->increment('quantity', $qtyToReturn);
                    Log::info('Sparepart ' . $sparepart->name . ' (ID: ' . $sparepartId . ') stock returned by ' . $qtyToReturn . ' as it was removed from transaction. New stock: ' . $sparepart->quantity);
                }
            }

            DB::commit();
            Log::info('Transaction update successfully committed (ID: ' . $transaction->id . ').');
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating transaction (ID: ' . $transaction->id . '): ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return redirect()->back()->with('error', 'Gagal mengedit transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load(['items.service', 'items.sparepart']);

        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        return view('pages.transaction.edit', compact('transaction', 'services', 'spareparts'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction
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
                    $sparepart = Sparepart::find($item->item_id);
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
        $transaction->load(['items.service', 'items.sparepart']);

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