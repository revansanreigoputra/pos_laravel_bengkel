<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\SupplierSparepartStock;
use App\Models\Supplier;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SupplierSparepartStockController extends Controller
{
    public function index()
    {
        $stocks = SupplierSparepartStock::with(['supplier', 'sparepart'])->get();
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();

        return view('pages.stock-handle.index', compact('stocks', 'suppliers', 'spareparts'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();

        return view('pages.stock-handle.create', compact('suppliers', 'spareparts'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'spareparts' => 'required|array|min:1',
        'spareparts.*.sparepart_id' => 'required|exists:spareparts,id',
        'spareparts.*.quantity' => 'required|numeric|min:1',
        'spareparts.*.purchase_price' => 'required|numeric|min:0',
        'spareparts.*.received_date' => 'nullable|date',
        'spareparts.*.note' => 'nullable|string',
    ]);

    foreach ($validated['spareparts'] as $sparepartInput) {
        $stock = SupplierSparepartStock::create([
            'supplier_id' => $validated['supplier_id'],
            'sparepart_id' => $sparepartInput['sparepart_id'],
            'quantity' => $sparepartInput['quantity'],
            'purchase_price' => $sparepartInput['purchase_price'],
            'received_date' => $sparepartInput['received_date'] ?? now(),
            'note' => $sparepartInput['note'] ?? null,
        ]);

        $sparepart = Sparepart::find($sparepartInput['sparepart_id']);
        $sparepart->update([
            'quantity' => $sparepart->stockBatches()->sum('quantity'),
            'purchase_price' => $sparepartInput['purchase_price'],
        ]);
    }

    return redirect()->route('stock-handle.index')->with('success', 'Stock successfully added.');
}


    public function edit(SupplierSparepartStock $stock)
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();

        return view('pages.stock-handle.edit', compact('stock', 'suppliers', 'spareparts'));
    }
    public function update(Request $request, SupplierSparepartStock $stock)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'received_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        // Update stock record
        $stock->update($validated);
        $sparepart = Sparepart::find($validated['sparepart_id']);
        $sparepart->quantity = $sparepart->stockBatches()->sum('quantity');

        $latestStock = $sparepart->stockBatches()->latest('created_at')->first();
        $sparepart->purchase_price = $latestStock?->purchase_price ?? 0;
        $sparepart->save();


        return redirect()->route('stock-handle.index')->with('success', 'Stock successfully updated.');
    }
    public function destroy(SupplierSparepartStock $stock)
    {

        // Delete stock record
        $stock->delete();

        $sparepart = Sparepart::find($stock->sparepart_id);
        $sparepart->quantity = $sparepart->stockBatches()->sum('quantity');
        $latestStock = $sparepart->stockBatches()->latest('created_at')->first();
        $sparepart->purchase_price = $latestStock?->purchase_price ?? 0;
        $sparepart->save();

        return redirect()->route('stock-handle.index')->with('success', 'Stock successfully deleted.');
    }
     //////add new sparepart just name and code 
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part',
        ]);

        $sparepart = Sparepart::create([
            'name' => $validated['name'],
            'code_part' => $validated['code_part'],
            'purchase_price' => 0,
            'selling_price' => 0,
            'discount_percentage' => 0,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'expired_date' => null,
            'quantity' => 0,
        ]);

       return redirect()->back()->with('success', 'Sparepart berhasil ditambahkan.');

    }
}
