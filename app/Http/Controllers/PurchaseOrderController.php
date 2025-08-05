<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Sparepart;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])->latest('order_date')->paginate(10);
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();
        return view('purchase_orders.create', compact('suppliers', 'spareparts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'invoice_number' => 'required|string|unique:purchase_orders,invoice_number|max:255',
                'supplier_id' => 'required|exists:suppliers,id',
                'order_date' => 'nullable|date',
                'payment_method' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|string|in:pending,received,canceled',
                'total_price' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.sparepart_id' => 'required|exists:spareparts,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.purchase_price' => 'required|numeric|min:0',
                'items.*.expired_date' => 'nullable|date|after_or_equal:order_date',
            ]);

            // Gunakan service untuk membuat purchase order
            $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder(
                collect($validatedData)->except('items')->toArray(),
                $validatedData['items']
            );

            return redirect()->route('purchase_orders.index')->with('success', 'Pesanan pembelian berhasil dibuat!');

        } catch (Exception $e) {
            Log::error('Error storing purchase order: ' . $e->getMessage());
            return redirect()->back()->withErrors("Gagal menyimpan pesanan pembelian: " . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.sparepart');
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();
        $purchaseOrder->load('items');
        return view('purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'spareparts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Validasi
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
            'total_price' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.expired_date' => 'nullable|date',
        ]);

        // Tambahkan validasi tanggal kedaluwarsa
        foreach ($validatedData['items'] as $item) {
            if ($item['expired_date'] && $item['expired_date'] < $validatedData['order_date']) {
                 throw new \Exception('Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal pesanan.');
            }
        }
        
        DB::beginTransaction();

        try {
            // Cek jika status berubah menjadi 'canceled', lakukan penyesuaian stok
            if ($purchaseOrder->status !== 'canceled' && $validatedData['status'] === 'canceled') {
                $this->purchaseOrderService->revertPurchaseOrderItems($purchaseOrder);
            }

            // Update data Purchase Order utama
            $purchaseOrder->update(collect($validatedData)->except('items')->toArray());

            $itemsToKeep = collect($validatedData['items'])->pluck('id')->filter()->all();
            
            // Hapus item lama yang tidak ada di request
            $purchaseOrder->items()->whereNotIn('id', $itemsToKeep)->delete();

            // Tambahkan item baru atau perbarui item yang ada
            foreach ($validatedData['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Perbarui item yang sudah ada
                    $purchaseOrder->items()->find($itemData['id'])->update($itemData);
                } else {
                    // Tambahkan item baru
                    $purchaseOrder->items()->create($itemData);
                }
            }

            DB::commit();

            return redirect()->route('purchase_orders.show', $purchaseOrder->id)->with('success', 'Pesanan pembelian berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating purchase order: ' . $e->getMessage());
            return redirect()->back()->withErrors("Gagal memperbarui pesanan pembelian: " . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::beginTransaction();
        try {
            // Periksa apakah ada item dari PO ini yang sudah terjual
            $hasSoldItems = $purchaseOrder->items()->whereHas('transactionItems')->exists();

            if ($hasSoldItems) {
                throw new Exception("Tidak dapat menghapus pesanan pembelian karena beberapa itemnya sudah terjual.");
            }

            // Jika tidak ada yang terjual, hapus PO
            $purchaseOrder->delete();
            DB::commit();
            return redirect()->route('purchase_orders.index')->with('success', 'Pesanan pembelian berhasil dihapus!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting purchase order: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus pesanan pembelian: ' . $e->getMessage()]);
        }
    }
}