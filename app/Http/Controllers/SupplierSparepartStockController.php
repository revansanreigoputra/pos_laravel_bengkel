<?php

namespace App\Http\Controllers;

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
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'received_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        // Create new stock record
        SupplierSparepartStock::create($validated);
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
