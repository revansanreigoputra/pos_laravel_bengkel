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
        $spareparts = Sparepart::all();

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
                'total_price'      => 0,
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

                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        if ($sparepart->stock >= $quantity) {
                            $sparepart->decrement('stock', $quantity);
                        } else {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi. Sisa stok: ' . $sparepart->stock)->withInput();
                        }
                    }
                }
            }

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
            $transaction->update([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
            ]);

            $currentItems = $transaction->items()->get();

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

            }

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
        try {
            $transaction->items()->delete();
            $transaction->delete();
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting transaction: ' . $e->getMessage()); // Corrected line
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