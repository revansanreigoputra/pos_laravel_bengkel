<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Import DB facade untuk transaksi database

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua pesanan pembelian.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])->latest('order_date')->paginate(10);
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan formulir untuk membuat pesanan pembelian baru.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();
        return view('purchase_orders.create', compact('suppliers', 'spareparts'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan pesanan pembelian baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data utama Purchase Order
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|unique:purchase_orders,invoice_number|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,received,canceled',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Validasi data items (Purchase Order Items)
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.expired_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Membuat entri PurchaseOrder baru di database.
            $purchaseOrder = PurchaseOrder::create($validatedData);

            // Menyimpan setiap item pesanan pembelian
            foreach ($request->input('items') as $itemData) {
                $purchaseOrder->items()->create($itemData);

                // Tambahkan stok ke tabel spareparts
                $sparepart = Sparepart::find($itemData['sparepart_id']);
                if ($sparepart) {
                    $sparepart->increment('stock', $itemData['quantity']);
                }
            }

            DB::commit();

            return redirect()->route('purchase_orders.index')->with('success', 'Pesanan pembelian berhasil dibuat!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withErrors("Gagal menyimpan pesanan pembelian: " . $th->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Menampilkan detail pesanan pembelian tertentu.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.sparepart');
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan formulir untuk mengedit pesanan pembelian tertentu.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();
        // Memuat item yang sudah ada untuk ditampilkan di form edit
        $purchaseOrder->load('items');
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'spareparts'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui pesanan pembelian tertentu di database.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validatedData = $request->validate([
            'invoice_number' => [
                'required',
                'string',
                Rule::unique('purchase_orders')->ignore($purchaseOrder->id),
                'max:255',
            ],
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,received,canceled',
            'global_discount' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
        ]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id', // Untuk item yang sudah ada
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.expired_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            if (empty($validatedData['order_date'])) {
                $validatedData['order_date'] = now();
            }

            $purchaseOrder->update([
                'invoice_number' => $validatedData['invoice_number'],
                'supplier_id' => $validatedData['supplier_id'],
                'order_date' => $validatedData['order_date'],
                'total_price' => $validatedData['total_price'],
                'payment_method' => $validatedData['payment_method'],
                'notes' => data_get($validatedData, 'notes'), // Menggunakan data_get untuk akses aman
                'status' => $validatedData['status'],
            ]);

            // Sinkronisasi item: Hapus item lama yang tidak ada di request, perbarui yang ada, tambahkan yang baru
            $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
            $itemsToKeep = [];

            foreach ($request->input('items') as $itemData) {
                // Menggunakan data_get untuk mengakses expired_date dan notes dengan aman
                $itemData['expired_date'] = data_get($itemData, 'expired_date');
                $itemData['notes'] = data_get($itemData, 'notes');

                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                    // Update item yang sudah ada
                    $item = $purchaseOrder->items()->find($itemData['id']);
                    if ($item) {
                        // **LOGIKA PENTING: Sesuaikan stok saat update**
                        // Hitung perbedaan kuantitas lama dan baru
                        $oldQuantity = $item->quantity;
                        $newQuantity = $itemData['quantity'];
                        $quantityDifference = $newQuantity - $oldQuantity;

                        $item->update($itemData);

                        $sparepart = Sparepart::find($itemData['sparepart_id']);
                        if ($sparepart) {
                            $sparepart->increment('stock', $quantityDifference); // Tambah/kurangi stok sesuai perbedaan
                        }
                        $itemsToKeep[] = $item->id;
                    }
                } else {
                    // Tambah item baru
                    $newItem = $purchaseOrder->items()->create($itemData);
                    $itemsToKeep[] = $newItem->id;

                    // **LOGIKA PENTING: Tambah stok untuk item baru**
                    $sparepart = Sparepart::find($itemData['sparepart_id']);
                    if ($sparepart) {
                        $sparepart->increment('stock', $itemData['quantity']);
                    }
                }
            }

            // Hapus item yang tidak lagi ada di request
            // **LOGIKA PENTING: Kurangi stok untuk item yang dihapus**
            $itemsToDelete = $purchaseOrder->items()->whereNotIn('id', $itemsToKeep)->get();
            foreach ($itemsToDelete as $item) {
                $sparepart = Sparepart::find($item->sparepart_id);
                if ($sparepart) {
                    $sparepart->decrement('stock', $item->quantity);
                }
                $item->delete();
            }


            DB::commit();

            return redirect()->route('purchase_orders.show', $purchaseOrder->id)->with('success', 'Pesanan pembelian berhasil diperbarui beserta itemnya!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui pesanan pembelian: ' . $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     * Menghapus pesanan pembelian tertentu dari database.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::beginTransaction();
        try {
            // **LOGIKA PENTING: Kurangi stok untuk semua item yang terkait dengan PO yang dihapus**
            foreach ($purchaseOrder->items as $item) {
                $sparepart = Sparepart::find($item->sparepart_id);
                if ($sparepart) {
                    $sparepart->decrement('stock', $item->quantity);
                }
            }

            // Item purchase_order_items akan otomatis terhapus karena cascadeOnDelete
            // Pastikan Anda sudah mengatur cascadeOnDelete di migrasi relasi purchase_order_id
            $purchaseOrder->delete();
            DB::commit();
            return redirect()->route('purchase_orders.index')->with('success', 'Pesanan pembelian berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus pesanan pembelian: ' . $e->getMessage()]);
        }
    }
}
