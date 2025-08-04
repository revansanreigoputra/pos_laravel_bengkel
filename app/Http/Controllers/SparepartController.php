<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category; // Pastikan ini diimpor jika Anda menggunakannya
use App\Models\Supplier; // Pastikan ini diimpor jika Anda menggunakannya
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // Pastikan Carbon diimpor

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar sumber daya.
     */
    public function index()
    {
        // Memuat relasi 'category' dan 'purchaseOrderItems'
        // dan mengurutkan purchaseOrderItems berdasarkan expired_date
        $spareparts = Sparepart::with(['category', 'supplier', 'purchaseOrderItems' => function($query) {
            $query->where('quantity', '>', 0) // Hanya item dengan stok > 0
                  ->where(function($q) {
                      $q->where('expired_date', '>=', Carbon::today()) // Belum kadaluarsa
                        ->orWhereNull('expired_date'); // Atau tidak ada tanggal kadaluarsa
                  })
                  ->orderBy('expired_date', 'asc'); // Urutkan untuk mendapatkan yang terdekat
        }])->latest()->paginate(10); // Atau gunakan get() jika tidak ada paginasi

        // Jika Anda memiliki filter atau pencarian, tambahkan di sini
        // $spareparts = Sparepart::query();
        // if ($request->has('search')) {
        //     $spareparts->where('name', 'like', '%' . $request->search . '%');
        // }
        // $spareparts = $spareparts->paginate(10);

        return view('pages.spareparts.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan formulir untuk membuat sumber daya baru.
     */
    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori
        $suppliers = Supplier::all(); // Ambil semua supplier
        return view('pages.spareparts.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan sumber daya yang baru dibuat ke penyimpanan.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0', // Harga beli
            'selling_price' => 'nullable|numeric|min:0', // Harga jual standar
            'stock' => 'nullable|integer|min:0', // Stok awal, jika diisi manual
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
        ]);
        
        $sparepart = Sparepart::create($validatedData);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan formulir untuk mengedit sumber daya yang ditentukan.
     */
    public function edit(Sparepart $sparepart)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('pages.spareparts.edit', compact('sparepart', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui sumber daya yang ditentukan di penyimpanan.
     */
    public function update(Request $request, Sparepart $sparepart)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part,' . $sparepart->id . '|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category_id' => 'required|exists:categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
        ]);

        $sparepart->update($validatedData);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus sumber daya yang ditentukan dari penyimpanan.
     */
    public function destroy(Sparepart $sparepart)
    {
        try {
            $sparepart->delete();
            return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('spareparts.index')->with('error', 'Gagal menghapus sparepart: ' . $e->getMessage());
        }
    }
}
