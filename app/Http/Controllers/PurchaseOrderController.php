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
        $query = PurchaseOrder::with(['supplier', 'items'])->latest('order_date');

        $purchaseOrders = $query->paginate(10);

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        
        // Ambil spareparts dengan harga beli terbaru dari pembelian sebelumnya
        $spareparts = Sparepart::select('spareparts.*')
            ->selectSub(function($query) {
                $query->select('purchase_order_items.purchase_price')
                    ->from('purchase_order_items')
                    ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                    ->whereColumn('purchase_order_items.sparepart_id', 'spareparts.id')
                    ->where('purchase_orders.status', '!=', 'canceled')
                    ->orderBy('purchase_orders.order_date', 'desc')
                    ->orderBy('purchase_orders.id', 'desc')
                    ->limit(1);
            }, 'latest_purchase_price')
            ->get()
            ->map(function ($sparepart) {
                // Gunakan harga terbaru jika ada, jika tidak gunakan harga default
                $sparepart->purchase_price = $sparepart->latest_purchase_price ?? $sparepart->purchase_price ?? 0;
                return $sparepart;
            });
        
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
                'global_discount' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.sparepart_id' => 'required|exists:spareparts,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.purchase_price' => 'required|numeric|min:0',
                'items.*.expired_date' => 'nullable|date|after_or_equal:order_date',
                'items.*.notes' => 'nullable|string',
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
        
        // Ambil spareparts dengan harga beli terbaru dari pembelian sebelumnya
        $spareparts = Sparepart::select('spareparts.*')
            ->selectSub(function($query) {
                $query->select('purchase_order_items.purchase_price')
                    ->from('purchase_order_items')
                    ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                    ->whereColumn('purchase_order_items.sparepart_id', 'spareparts.id')
                    ->where('purchase_orders.status', '!=', 'canceled')
                    ->orderBy('purchase_orders.order_date', 'desc')
                    ->orderBy('purchase_orders.id', 'desc')
                    ->limit(1);
            }, 'latest_purchase_price')
            ->get()
            ->map(function ($sparepart) {
                // Gunakan harga terbaru jika ada, jika tidak gunakan harga default
                $sparepart->purchase_price = $sparepart->latest_purchase_price ?? $sparepart->purchase_price ?? 0;
                return $sparepart;
            });
        
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
            'global_discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.expired_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string',
        ]);

        // Validasi tanggal kedaluwarsa
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

            // Proses item-item yang ada di request
            $existingItemIds = [];
            foreach ($validatedData['items'] as $itemData) {
                if (isset($itemData['id']) && !empty($itemData['id'])) {
                    // Update existing item
                    $item = $purchaseOrder->items()->find($itemData['id']);
                    if ($item) {
                        $item->update($itemData);
                        $existingItemIds[] = $item->id;
                    }
                } else {
                    // Create new item
                    $newItem = $purchaseOrder->items()->create($itemData);
                    $existingItemIds[] = $newItem->id;
                }
            }

            // Hapus item yang tidak ada di request
            $purchaseOrder->items()->whereNotIn('id', $existingItemIds)->delete();

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
            $hasSoldItems = $purchaseOrder->items()->whereHas('sparepart.transactionItems')->exists();

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

    /**
     * API endpoint untuk mendapatkan harga terbaru sparepart
     */
    public function getLatestPrice($sparepartId)
    {
        try {
            // Ambil harga dari purchase order terbaru
            $latestPrice = DB::table('purchase_order_items')
                ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                ->where('purchase_order_items.sparepart_id', $sparepartId)
                ->where('purchase_orders.status', '!=', 'canceled')
                ->orderBy('purchase_orders.order_date', 'desc')
                ->orderBy('purchase_orders.id', 'desc')
                ->first();
            
            if ($latestPrice) {
                return response()->json([
                    'latest_price' => $latestPrice->purchase_price,
                    'order_date' => $latestPrice->order_date
                ]);
            }
            
            // Jika tidak ada, coba ambil dari master sparepart
            $sparepart = Sparepart::find($sparepartId);
            return response()->json([
                'latest_price' => $sparepart->purchase_price ?? 0,
                'order_date' => null
            ]);
        } catch (\Exception $e) {
            return response()->json(['latest_price' => 0, 'order_date' => null]);
        }
    }

    /**
     * Cek apakah nomor invoice sudah ada
     */
    public function checkInvoiceNumber(Request $request)
    {
        $exists = PurchaseOrder::where('invoice_number', $request->invoice_number)->exists();
        return response()->json(['exists' => $exists]);
    }
}