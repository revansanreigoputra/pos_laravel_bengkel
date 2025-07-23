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
        // Eager load relationships to avoid N+1 query problem for items, services, and spareparts
        $transactions = Transaction::with(['items.service', 'items.sparepart'])->latest()->get();

        // Fetch active services and all spareparts for the "create" and "edit" forms
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
        // Fetch necessary data for the create form (services and spareparts)
        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        // Return the dedicated create view
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
            // Validate incoming request data
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:255',
                'transaction_date' => 'required|date', // Added validation for transaction_date
                'items' => 'required|array|min:1', // Ensure at least one item is present
                'items.*.item_full_id' => 'required|string', // e.g., "service-1" or "sparepart-5"
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0', // Total price from frontend calculation
            ]);
        } catch (ValidationException $e) {
            // If validation fails, redirect back with errors and old input
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction(); // Start database transaction for atomicity
        try {
            $subTotalCalculated = 0; // Initialize subtotal for backend calculation

            // Calculate subtotal based on the items received from the frontend
            foreach ($validatedData['items'] as $itemData) {
                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);
                $subTotalCalculated += ($price * $quantity);
            }

            $globalDiscount = floatval($request->input('global_discount', 0));
            $finalTotalCalculated = $subTotalCalculated - $globalDiscount;

            // Ensure the final total doesn't go below zero
            if ($finalTotalCalculated < 0) {
                $finalTotalCalculated = 0;
            }

            // Compare frontend total_price with backend calculated total_price
            // A small tolerance (0.01) is used for floating-point comparison to account for precision differences
            $frontendTotalPrice = floatval($validatedData['total_price']);
            if (abs($finalTotalCalculated - $frontendTotalPrice) > 0.01) {
                Log::warning("Frontend total_price ({$frontendTotalPrice}) differs from backend calculated total_price ({$finalTotalCalculated}). Using backend calculated total.");
                // In case of discrepancy, prioritize the backend calculated value for data integrity
                $finalTotalToSave = $finalTotalCalculated;
            } else {
                // If consistent, use the frontend value
                $finalTotalToSave = $frontendTotalPrice;
            }

            // Create the new Transaction record
            $transaction = Transaction::create([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
                'transaction_date' => $validatedData['transaction_date'], // Use the date from the form
                'discount_amount'  => $globalDiscount, // Store the global discount amount
                'total_price'      => $finalTotalToSave, // Store the final calculated total
            ]);

            // Iterate through each item and create a TransactionItem record
            foreach ($validatedData['items'] as $itemData) {
                // Split 'item_full_id' (e.g., "service-1" or "sparepart-5") into type and ID
                [$type, $id] = explode('-', $itemData['item_full_id']);

                $price = floatval($itemData['price']);
                $quantity = intval($itemData['quantity']);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $type,
                    'item_id'        => $id,
                    'price'          => $price, // Store the price at the time of transaction
                    'quantity'       => $quantity,
                ]);

                // Manage sparepart stock: decrement if item is a sparepart
                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        // Check if sufficient stock is available before decrementing
                        if ($sparepart->quantity >= $quantity) {
                            $sparepart->decrement('quantity', $quantity); // Decrease stock
                        } else {
                            // If stock is insufficient, rollback transaction and return error
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi. Sisa stok: ' . $sparepart->quantity)->withInput();
                        }
                    } else {
                        // If sparepart not found, rollback and return error
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan.')->withInput();
                    }
                }
            }

            DB::commit(); // Commit the database transaction if all operations are successful
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction on any error
            Log::error('Error storing transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     * This method is typically used for updating via a modal or a dedicated edit page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        try {
            // Validate incoming request data for update
            $validatedData = $request->validate([
                'customer_name' => 'required|string|max:255',
                'vehicle_number' => 'required|string|max:255',
                'transaction_date' => 'required|date', // ADDED: Validation for transaction_date in update
                'items' => 'required|array|min:1',
                'items.*.item_full_id' => 'required|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'global_discount' => 'nullable|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction(); // Start database transaction
        try {
            // Store old sparepart quantities to manage stock adjustments correctly
            // This is crucial to correctly return/deduct stock when items change
            $oldSparepartQuantities = [];
            foreach ($transaction->items as $oldItem) {
                if ($oldItem->item_type === 'sparepart') {
                    // Sum quantities for the same sparepart ID if it appeared multiple times
                    $oldSparepartQuantities[$oldItem->item_id] = ($oldSparepartQuantities[$oldItem->item_id] ?? 0) + $oldItem->quantity;
                }
            }

            // Recalculate subtotal and final total on the backend for verification
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

            // Compare frontend total_price with backend calculated total_price
            $frontendTotalPrice = floatval($validatedData['total_price']);
            if (abs($finalTotalCalculated - $frontendTotalPrice) > 0.01) {
                Log::warning("Frontend total_price ({$frontendTotalPrice}) differs from backend calculated total_price ({$finalTotalCalculated}) during update. Using backend calculated total.");
                $finalTotalToSave = $finalTotalCalculated;
            } else {
                $finalTotalToSave = $frontendTotalPrice;
            }

            // Update the main transaction details
            $transaction->update([
                'customer_name'    => $validatedData['customer_name'],
                'vehicle_number'   => $validatedData['vehicle_number'],
                'transaction_date' => $validatedData['transaction_date'], // ADDED: Update transaction_date
                'discount_amount'  => $globalDiscount,
                'total_price'      => $finalTotalToSave,
            ]);

            // Delete all existing transaction items for this transaction
            // This simplifies the update logic: remove all and then re-add based on new data
            $transaction->items()->delete();

            // Recreate transaction items with new data and adjust sparepart stock
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

                // Manage sparepart stock for new/updated items
                if ($type === 'sparepart') {
                    $sparepart = Sparepart::find($id);
                    if ($sparepart) {
                        // Calculate the net change in quantity
                        // If sparepart was in old transaction, its old quantity is deducted from new quantity
                        $oldQuantityForThisSparepart = $oldSparepartQuantities[$id] ?? 0;
                        $netChange = $quantity - $oldQuantityForThisSparepart;

                        if ($netChange > 0) { // If new quantity is higher, decrement stock
                            if ($sparepart->quantity >= $netChange) {
                                $sparepart->decrement('quantity', $netChange);
                            } else {
                                // Rollback if stock is insufficient for the *net increase*
                                DB::rollBack();
                                return redirect()->back()->with('error', 'Stok sparepart ' . $sparepart->name . ' tidak mencukupi untuk perubahan ini. Sisa stok: ' . $sparepart->quantity)->withInput();
                            }
                        } elseif ($netChange < 0) { // If new quantity is lower, increment stock (return to inventory)
                            $sparepart->increment('quantity', abs($netChange));
                        }
                        // If netChange is 0, no stock adjustment is needed for this specific sparepart
                    } else {
                        // Rollback if a sparepart listed in the update cannot be found
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Sparepart dengan ID ' . $id . ' tidak ditemukan saat update.')->withInput();
                    }
                    // Remove this sparepart from the list of old quantities that need to be returned
                    // This ensures only truly removed spareparts have their stock returned later
                    unset($oldSparepartQuantities[$id]);
                }
            }

            // Return stock for any spareparts that were in the old transaction
            // but are completely *removed* from the updated transaction
            foreach ($oldSparepartQuantities as $sparepartId => $qtyToReturn) {
                $sparepart = Sparepart::find($sparepartId);
                if ($sparepart) {
                    $sparepart->increment('quantity', $qtyToReturn);
                }
            }

            DB::commit(); // Commit the transaction if all operations succeed
            // MODIFIED: Redirect to transaction.index upon successful update
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback(); // Rollback on any exception
            Log::error('Error updating transaction: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengedit transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Transaction $transaction)
    {
        // Eager load items, service, and sparepart to prevent N+1 queries
        $transaction->load(['items.service', 'items.sparepart']);

        // Fetch necessary data for the edit form
        $services = Service::where('status', 'aktif')->get();
        $spareparts = Sparepart::all();

        // Pass the transaction to the view for pre-filling the form
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
        DB::beginTransaction(); // Start database transaction
        try {
            // Before deleting the transaction, return sparepart stock to inventory
            foreach ($transaction->items as $item) {
                if ($item->item_type === 'sparepart') {
                    $sparepart = Sparepart::find($item->item_id);
                    if ($sparepart) {
                        $sparepart->increment('quantity', $item->quantity);
                    }
                }
            }

            $transaction->items()->delete(); // Delete all associated transaction items
            $transaction->delete(); // Delete the transaction itself

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
    public function exportPdf()
    {
        return 'Fitur export PDF belum dibuat';
    }
}