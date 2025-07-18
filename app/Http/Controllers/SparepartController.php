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
        // Assuming 'stockBatches' is a relationship on your Sparepart model
        $spareparts = Sparepart::has('stockBatches')->with('stockBatches')->get();

        return view('pages.sparepart.index', compact('spareparts'));
    }

    public function create()
    {
        // If supplier_id is required for a Sparepart, you'll need to pass suppliers to the view.
        // For example: $suppliers = Supplier::all();
        // return view('pages.sparepart.create', compact('suppliers'));
        return view('pages.sparepart.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part',
            'selling_price' => 'required|numeric|min:0',
            // Add new discount fields for initial creation if they can be set at creation
            'discount_percentage' => 'nullable|numeric|min:0|max:100', // Added
            'discount_start_date' => 'nullable|date', // Added
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date', // Added
            'expired_date' => 'nullable|date',
            // 'supplier_id' => 'required|exists:suppliers,id', // <-- IMPORTANT: If supplier_id is NOT NULL in your spareparts table, uncomment this and add it to your form.
        ]);

        Sparepart::create([
            'name' => $validated['name'],
            'code_part' => $validated['code_part'],
            'purchase_price' => 0, // Still set to 0 as per your previous logic
            'selling_price' => $validated['selling_price'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0, // Use null coalescing for optional fields
            'discount_start_date' => $validated['discount_start_date'],
            'discount_end_date' => $validated['discount_end_date'],
            'expired_date' => $validated['expired_date'],
            'quantity' => 0, // Start with 0; will be updated by SupplierSparepartStock
            // 'supplier_id' => $validated['supplier_id'], // <-- IMPORTANT: If uncommented above, uncomment this too.
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
            'selling_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100', // Added
            'discount_start_date' => 'nullable|date', // Added
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date', // Added
            'expired_date' => 'nullable|date',
            // 'quantity' is removed from direct update via this form
            // As per your previous comment, quantity should be updated by SupplierSparepartStock processes.
            // If you still want to allow direct quantity edits, uncomment the line below:
            // 'quantity' => 'required|numeric|min:0',
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