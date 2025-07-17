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
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'sparepart_id' => 'nullable|exists:spareparts,id',
            'quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'received_date' => 'nullable|date',
            'note' => 'nullable|string',
            'selling_price' => 'nullable|numeric|min:0',
        ];

        // Conditionally require name & code_part if sparepart_id is not provided
        if (!$request->filled('sparepart_id')) {
            $rules['name'] = 'required|string|max:255';
            $rules['code_part'] = 'required|string|unique:spareparts,code_part';
        }

        $validated = Validator::make($request->all(), $rules)->validate();

        // If sparepart_id not selected, create new sparepart
        if (empty($validated['sparepart_id'])) {
            $sparepart = Sparepart::create([
                'name' => $validated['name'],
                'code_part' => $validated['code_part'],
                'purchase_price' => 0, // will be updated below
                'selling_price' => $validated['selling_price'] ?? 0,
                'expired_date' => null,
                'quantity' => 0,
            ]);
            $validated['sparepart_id'] = $sparepart->id;
        }

        // Create new stock record
        SupplierSparepartStock::create([
            'supplier_id' => $validated['supplier_id'],
            'sparepart_id' => $validated['sparepart_id'],
            'quantity' => $validated['quantity'],
            'purchase_price' => $validated['purchase_price'],
            'received_date' => $validated['received_date'],
            'note' => $validated['note'] ?? null,
        ]);

        // Update sparepart quantity and purchase price
        $sparepart = Sparepart::find($validated['sparepart_id']);
        $sparepart->quantity = $sparepart->stockBatches()->sum('quantity');
        $latestStock = $sparepart->stockBatches()->latest('created_at')->first();
        $sparepart->purchase_price = $latestStock?->purchase_price ?? 0;
        $sparepart->save();

        return redirect()->back()->with('success', 'Stock successfully added.');
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
}
