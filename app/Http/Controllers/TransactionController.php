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
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
                // --- Tambahan Validasi ---
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50', // Contoh: 'tunai', 'transfer bank', 'kartu debit', 'e-wallet'
                'proof_of_transfer_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'status' => 'required|in:pending,completed,cancelled', // Sesuai enum di DB
            ]);
        } catch (ValidationException $e) {
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
            $globalDiscount = floatval($request->input('global_discount', 0));
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
                $file->storeAs('public/proof_of_transfer', $fileName); // Simpan di storage/app/public/proof_of_transfer
                $proofOfTransferUrl = 'storage/proof_of_transfer/' . $fileName; // Path yang bisa diakses publik
            }

            $transaction = Transaction::create([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
                'transaction_date' => $validatedData['transaction_date'],
                'discount_amount'  => $globalDiscount,
                'total_price'      => $finalTotalToSave,
                // --- Tambahan Data Transaksi ---
                'invoice_number' => $validatedData['invoice_number'],
                'vehicle_model' => $validatedData['vehicle_model'],
                'payment_method' => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status' => $validatedData['status'],
            ]);

            // ... (logic for transaction items remains the same)
            foreach ($validatedData['items'] as $itemData) {
                // ... (existing logic for splitting type, id, price, quantity)
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $type,
                    'item_id'        => $id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                ]);

                // ... (existing stock management logic)
                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        if ($sparepart->quantity >= $quantity) {
                            $sparepart->decrement('quantity', $quantity);
                        } else {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi. Sisa stok: ' . $sparepart->quantity)->withInput();
                        }
                    } else {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan.')->withInput();
                    }
                }
            }

            DB::commit();
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing transaction: ' . $e->getMessage());
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
                // --- Tambahan Validasi ---
                'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $transaction->id, // Unique kecuali ID ini sendiri
                'vehicle_model' => 'nullable|string|max:255',
                'payment_method' => 'required|string|max:50',
                'proof_of_transfer_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
                'status' => 'required|in:pending,completed,cancelled',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $oldSparepartQuantities = [];
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $oldSparepartQuantities[$oldItem->item_id] = ($oldSparepartQuantities[$oldItem->item_id] ?? 0) + $oldItem->quantity;
                }
            }

            $subTotalCalculated = 0;
            foreach ($validatedData['items'] as $itemData) {
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);
                $subTotalCalculated += ($price * $quantity);
            }
            $globalDiscount = floatval($request->input('global_discount', 0));
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
                if ($proofOfTransferUrl && file_exists(public_path($proofOfTransferUrl))) {
                    unlink(public_path($proofOfTransferUrl));
                }
                $file = $request->file('proof_of_transfer_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/proof_of_transfer', $fileName);
                $proofOfTransferUrl = 'storage/proof_of_transfer/' . $fileName;
            } elseif ($request->boolean('clear_proof_of_transfer')) { 
                if ($proofOfTransferUrl && file_exists(public_path($proofOfTransferUrl))) {
                    unlink(public_path($proofOfTransferUrl));
                }
                $proofOfTransferUrl = null;
            }


            $transaction->update([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
                'transaction_date' => $validatedData['transaction_date'],
                'discount_amount'  => $globalDiscount,
                'total_price'      => $finalTotalToSave,
                // --- Tambahan Data Transaksi ---
                'invoice_number' => $validatedData['invoice_number'],
                'vehicle_model' => $validatedData['vehicle_model'],
                'payment_method' => $validatedData['payment_method'],
                'proof_of_transfer_url' => $proofOfTransferUrl,
                'status' => $validatedData['status'],
            ]);

            $transaction->items()->delete();
            foreach ($validatedData['items'] as $itemData) {
                [$type, $id] = explode('-', $itemData['item_full_id']);
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $type,
                    'item_id'        => $id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                ]);

                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        $oldQuantityForThisSparepart = $oldSparepartQuantities[$id] ?? 0;
                        $netChange = $quantity - $oldQuantityForThisSparepart;
                        if ($netChange > 0) {
                            if ($sparepart->quantity >= $netChange) {
                                $sparepart->decrement('quantity', $netChange);
                            } else {
                                DB::rollBack();
                                return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi untuk perubahan ini. Sisa stok: ' . $sparepart->quantity)->withInput();
                            }
                        } elseif ($netChange < 0) {
                            $sparepart->increment('quantity', abs($netChange));
                        }
                        unset($oldSparepartQuantities[$id]);
                    } else {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan saat update.')->withInput();
                    }
                }
            }
            foreach ($oldSparepartQuantities as $sparepartId => $qtyToReturn) {
                $sparepart = Sparepart::find($sparepartId);
                if ($sparepart) {
                    $sparepart->increment('quantity', $qtyToReturn);
                }
            }

            DB::commit();
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating transaction: ' . $e->getMessage());
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
            foreach ($transaction->items as $item) {
                if ($item->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($item->item_id);
                    if ($sparepart) {
                        $sparepart->increment('quantity', $item->quantity);
                    }
                }
            }

            $transaction->items()->delete();
            $transaction->delete();

            DB::commit(); // Commit the transaction
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback(); // Rollback on error
            Log::error('Error deleting transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Export transactions to PDF (placeholder).
     *
     * @return \Illuminate\Http\Response
     */
    
    public function exportPdf(Transaction $transaction) // Ambil transaksi berdasarkan ID
    {
        $transaction->load(['items.service', 'items.sparepart']);

        $data = [
            'transaction' => $transaction,
            'nama_bengkel' => 'Bengkel XYZ',
            'alamat_bengkel' => 'Jl. Contoh No. 123, Yogyakarta',
            'telepon_bengkel' => '0812-3456-7890',
        ];

        $pdf = Pdf::loadView('pages.transaction.invoice_pdf', $data);
        return $pdf->download('invoice-' . $transaction->invoice_number . '.pdf');
    }

}