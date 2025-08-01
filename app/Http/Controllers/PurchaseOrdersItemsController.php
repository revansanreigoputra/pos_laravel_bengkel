<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder; // Import model PurchaseOrder
use App\Models\Sparepart;     // Import model Sparepart
use Illuminate\Http\Request;

class PurchaseOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua item pesanan pembelian.
     */
    public function index()
    {
        // Mengambil semua item pesanan pembelian dengan relasi purchaseOrder dan sparepart
        // secara eager loading, lalu dipaginasi.
        $purchaseOrderItems = PurchaseOrderItem::with(['purchaseOrder', 'sparepart'])->latest()->paginate(10);
        return view('purchase_order_items.index', compact('purchaseOrderItems'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan formulir untuk membuat item pesanan pembelian baru.
     * Biasanya, item ini dibuat dalam konteks PurchaseOrder tertentu.
     */
    public function create(Request $request)
    {
        // Mengambil semua PurchaseOrder dan Sparepart untuk dropdown
        $purchaseOrders = PurchaseOrder::all();
        $spareparts = Sparepart::all();

        // Jika ada purchase_order_id di query string, gunakan sebagai nilai default
        $purchaseOrderId = $request->query('purchase_order_id');

        return view('purchase_order_items.create', compact('purchaseOrders', 'spareparts', 'purchaseOrderId'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan item pesanan pembelian baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari request.
        $validatedData = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id', // Harus ada di tabel purchase_orders
            'sparepart_id' => 'required|exists:spareparts,id',         // Harus ada di tabel spareparts
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Membuat entri PurchaseOrderItem baru di database.
        PurchaseOrderItem::create($validatedData);

        // Mengarahkan kembali ke halaman detail PurchaseOrder terkait atau index.
        // Disarankan ke detail PurchaseOrder agar bisa melihat item yang baru ditambahkan.
        return redirect()->route('purchase_orders.show', $validatedData['purchase_order_id'])->with('success', 'Item pesanan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Menampilkan detail item pesanan pembelian tertentu.
     */
    public function show(PurchaseOrderItem $purchaseOrderItem)
    {
        // Memuat relasi purchaseOrder dan sparepart untuk ditampilkan di detail.
        $purchaseOrderItem->load('purchaseOrder', 'sparepart');
        return view('purchase_order_items.show', compact('purchaseOrderItem'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan formulir untuk mengedit item pesanan pembelian tertentu.
     */
    public function edit(PurchaseOrderItem $purchaseOrderItem)
    {
        // Mengambil semua PurchaseOrder dan Sparepart untuk dropdown di formulir edit.
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
        // Validasi data yang masuk untuk pembaruan.
        $validatedData = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Memperbarui entri PurchaseOrderItem di database.
        $purchaseOrderItem->update($validatedData);

        // Mengarahkan kembali ke halaman detail PurchaseOrder terkait atau index.
        return redirect()->route('purchase_orders.show', $validatedData['purchase_order_id'])->with('success', 'Item pesanan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus item pesanan pembelian tertentu dari database.
     */
    public function destroy(PurchaseOrderItem $purchaseOrderItem)
    {
        // Simpan purchase_order_id sebelum menghapus item
        $purchaseOrderId = $purchaseOrderItem->purchase_order_id;

        // Menghapus entri PurchaseOrderItem dari database.
        $purchaseOrderItem->delete();

        // Mengarahkan kembali ke halaman detail PurchaseOrder terkait atau index.
        return redirect()->route('purchase_orders.show', $purchaseOrderId)->with('success', 'Item pesanan berhasil dihapus!');
    }
}