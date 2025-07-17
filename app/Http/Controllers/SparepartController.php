<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
{
    // Only get spareparts that are used in supplier stock
    $spareparts = Sparepart::has('stockBatches')->with('stockBatches')->get();

    return view('pages.sparepart.index', compact('spareparts'));
}


    public function create()
    {
        return view('pages.sparepart.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part',
            // 'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
        ]);

        Sparepart::create([
            'name' => $validated['name'],
            'code_part' => $validated['code_part'],
            'purchase_price' => 0,
            'selling_price' => $validated['selling_price'],
            'expired_date' => $validated['expired_date'],
            'quantity' => 0, // Start with 0; will be updated by SupplierSparepartStock
        ]);

        return redirect()->route('sparepart.index')->with('success', 'Sparepart created successfully');
    }

    public function edit(Sparepart $sparepart)
    {
        return view('pages.sparepart.edit', compact('sparepart'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part,' . $sparepart->id,
            // 'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
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
