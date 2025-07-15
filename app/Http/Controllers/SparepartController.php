<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
    {
        $spareparts = Sparepart::get();
        $suppliers = Supplier::all();
        return view('pages.sparepart.index', compact('spareparts', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('pages.sparepart.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0'
        ]);

        $sparepart = Sparepart::create([
            'name' => $validated['name'],
            'code_part' => $validated['code_part'],
            'supplier_id' => $validated['supplier_id'],
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'expired_date' => $validated['expired_date'],
           'quantity' => $validated['quantity'],
        ]);

        return redirect()->route('sparepart.index')->with('success', 'Sparepart created successfully');
    }

     

    public function edit(Sparepart $sparepart)
    {
        $suppliers = Supplier::all();
        return view('sparepart.edit', compact('sparepart', 'suppliers'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part,' . $sparepart->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date'
        ]);

        $sparepart->update($validated);

        return redirect()->route('sparepart.index')->with('success', 'Sparepart updated successfully');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('sparepart.index')->with('success', 'Sparepart deleted successfully');
    }
}
