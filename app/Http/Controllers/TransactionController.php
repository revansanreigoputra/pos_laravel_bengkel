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
        $spareparts = Sparepart::all(); // Pastikan ini mengambil semua sparepart untuk dropdown

        return view('pages.transaction.index', compact('transactions', 'services', 'spareparts'));
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
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0', 
                'items.*.quantity' => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
                'transaction_date' => now(),
                'total_price'      => 0, // Akan diupdate setelah menghitung total item
            ]);

            $total = 0;
            
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

                $total += $price * $quantity;

                // Mengelola stok sparepart
                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        // Menggunakan 'quantity' sesuai dengan nama kolom di tabel 'spareparts'
                        if ($sparepart->quantity >= $quantity) {
                            $sparepart->decrement('quantity', $quantity); // Mengurangi stok
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

            // Update total_price transaksi setelah semua item ditambahkan
            $transaction->update(['total_price' => $total]);

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        try {
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // Simpan stok lama untuk sparepart yang mungkin dikembalikan
            $oldSparepartQuantities = [];
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    $oldSparepartQuantities[$oldItem->item_id] = ($oldSparepartQuantities[$oldItem->item_id] ?? 0) + $oldItem->quantity;
                }
            }

            // Update data transaksi
            $transaction->update([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
            ]);

            // Hapus semua item transaksi lama
            $transaction->items()->delete();

            $total = 0; 
            
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
                $total += $price * $quantity;

                // Mengelola stok sparepart untuk item baru
                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        // Jika sparepart ini ada di transaksi lama, kurangi dari stok lama yang akan dikembalikan
                        $oldQuantityForThisSparepart = $oldSparepartQuantities[$id] ?? 0;
                        $netChange = $quantity - $oldQuantityForThisSparepart;

                        if ($netChange > 0) { // Jika kuantitas baru lebih besar, kurangi stok
                            if ($sparepart->quantity >= $netChange) {
                                $sparepart->decrement('quantity', $netChange);
                            } else {
                                DB::rollBack();
                                return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi untuk perubahan ini. Sisa stok: ' . $sparepart->quantity)->withInput();
                            }
                        } elseif ($netChange < 0) { // Jika kuantitas baru lebih kecil, kembalikan stok
                            $sparepart->increment('quantity', abs($netChange));
                        }
                        // Jika netChange == 0, tidak ada perubahan stok
                    } else {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan saat update.')->withInput();
                    }
                    // Hapus dari daftar stok lama yang perlu dikembalikan
                    unset($oldSparepartQuantities[$id]);
                }
            }

            // Kembalikan stok untuk sparepart yang dihapus dari transaksi
            foreach ($oldSparepartQuantities as $sparepartId => $qtyToReturn) {
                $sparepart = Sparepart::find($sparepartId);
                if ($sparepart) {
                    $sparepart->increment('quantity', $qtyToReturn);
                }
            }

            // Update total_price transaksi
            $transaction->update(['total_price' => $total]);

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback(); 
            Log::error('Error updating transaction: ' . $e->getMessage()); 
            return redirect()->back()->with('error', 'Gagal mengedit transaksi: ' . $e->getMessage())->withInput();
        }
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
            // Kembalikan stok sparepart sebelum menghapus transaksi
            foreach ($transaction->items as $item) {
                if ($item->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($item->item_id);
                    if ($sparepart) {
                        $sparepart->increment('quantity', $item->quantity);
                    }
                }
            }

            $transaction->items()->delete(); // Hapus item-item transaksi
            $transaction->delete(); // Hapus transaksi itu sendiri

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting transaction: ' . $e->getMessage()); 
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Export transactions to PDF (placeholder).
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        return 'Fitur export PDF belum dibuat';
    }
}