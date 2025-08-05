<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PurchaseOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua item pesanan pembelian.
     */
    public function index()
    {
        $purchaseOrderItems = PurchaseOrderItem::with(['purchaseOrder', 'sparepart'])->latest()->paginate(10);
        return view('purchase_order_items.index', compact('purchaseOrderItems'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan formulir untuk membuat item pesanan pembelian baru.
     */
    public function create(Request $request)
    {
        $purchaseOrders = PurchaseOrder::all();
        $spareparts = Sparepart::all();
        $purchaseOrderId = $request->query('purchase_order_id');
        return view('purchase_order_items.create', compact('purchaseOrders', 'spareparts', 'purchaseOrderId'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan item pesanan pembelian baru ke database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi data yang masuk dari request.
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'sparepart_id' => 'required|exists:spareparts,id',
                'quantity' => 'required|integer|min:1',
                'purchase_price' => 'required|numeric|min:0',
                'expired_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $purchaseOrder = PurchaseOrder::find($validatedData['purchase_order_id']);

            // Validasi tanggal kedaluwarsa terhadap tanggal pesanan
            if ($validatedData['expired_date'] && $purchaseOrder->order_date && $validatedData['expired_date'] < $purchaseOrder->order_date) {
                throw new Exception("Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal pesanan.");
            }

            PurchaseOrderItem::create($validatedData);
            DB::commit();

            return redirect()->route('purchase_orders.show', $validatedData['purchase_order_id'])->with('success', 'Item pesanan berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing purchase order item: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan item pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Menampilkan detail item pesanan pembelian tertentu.
     */
    public function show(PurchaseOrderItem $purchaseOrderItem)
    {
        $purchaseOrderItem->load('purchaseOrder', 'sparepart');
        return view('purchase_order_items.show', compact('purchaseOrderItem'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan formulir untuk mengedit item pesanan pembelian tertentu.
     */
    public function edit(PurchaseOrderItem $purchaseOrderItem)
    {
        $purchaseOrders = PurchaseOrder::all();
        $spareparts = Sparepart::all();
        return view('purchase_order_items.edit', compact('purchaseOrderItem', 'purchaseOrders', 'spareparts'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui item pesanan pembelian tertentu di database.
     */
    public function update(Request $request, PurchaseOrderItem $purchaseOrderItem)
    {
        DB::beginTransaction();
        try {
            // Validasi data yang masuk untuk pembaruan.
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'sparepart_id' => 'required|exists:spareparts,id',
                'quantity' => 'required|integer|min:1',
                'purchase_price' => 'required|numeric|min:0',
                'expired_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            $purchaseOrder = PurchaseOrder::find($validatedData['purchase_order_id']);

            // Validasi tanggal kedaluwarsa terhadap tanggal pesanan
            if ($validatedData['expired_date'] && $purchaseOrder->order_date && $validatedData['expired_date'] < $purchaseOrder->order_date) {
                throw new Exception("Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal pesanan.");
            }

            // --- Poin Krusial: Proteksi Data Terjual ---
            // Cek apakah item ini sudah ada yang terjual (digunakan dalam transaksi)
            if ($purchaseOrderItem->transactionItems()->exists()) {
                // Jika kuantitas baru lebih kecil dari kuantitas awal
                if ($validatedData['quantity'] < $purchaseOrderItem->quantity) {
                    throw new Exception("Tidak dapat mengurangi kuantitas item yang sudah terjual.");
                }
                // Jika ID sparepart diubah
                if ($validatedData['sparepart_id'] != $purchaseOrderItem->sparepart_id) {
                    throw new Exception("Tidak dapat mengubah sparepart untuk item yang sudah terjual.");
                }
            }

            // Memperbarui entri PurchaseOrderItem di database.
            $purchaseOrderItem->update($validatedData);
            DB::commit();

            return redirect()->route('purchase_orders.show', $validatedData['purchase_order_id'])->with('success', 'Item pesanan berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase order item: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui item pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus item pesanan pembelian tertentu dari database.
     */
    public function destroy(PurchaseOrderItem $purchaseOrderItem)
    {
        DB::beginTransaction();
        try {
            // Simpan purchase_order_id sebelum menghapus item
            $purchaseOrderId = $purchaseOrderItem->purchase_order_id;

            // --- Poin Krusial: Proteksi Data Terjual ---
            // Cek apakah item ini sudah ada yang terjual (digunakan dalam transaksi)
            if ($purchaseOrderItem->transactionItems()->exists()) {
                throw new Exception("Tidak dapat menghapus item pesanan yang sudah terjual.");
            }

            // Menghapus entri PurchaseOrderItem dari database.
            $purchaseOrderItem->delete();
            DB::commit();

            return redirect()->route('purchase_orders.show', $purchaseOrderId)->with('success', 'Item pesanan berhasil dihapus!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting purchase order item: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus item pesanan: ' . $e->getMessage()]);
        }
    }
}