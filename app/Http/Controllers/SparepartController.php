<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar sumber daya.
     */
    public function index(Request $request)
    {
        // Mulai query untuk model Sparepart.
        $spareparts = Sparepart::query();

        // Eager load relasi yang diperlukan.
        $spareparts->with([
            'category',
            'supplier',
            // Load purchaseOrderItems untuk mendapatkan stok dan tanggal kedaluwarsa
            'purchaseOrderItems' => function ($query) {
                // Urutkan berdasarkan tanggal pembuatan terbaru untuk harga beli terbaru
                $query->latest();
            }
        ]);

        // Tambahkan filter pencarian berdasarkan nama sparepart jika ada input pencarian.
        if ($request->has('search') && !empty($request->search)) {
            $spareparts->where('name', 'like', '%' . $request->search . '%');
        }

        // Terapkan filter utama:
        // 1. Ambil sparepart yang memiliki purchaseOrderItems.
        // 2. Di dalam purchaseOrderItems, cek item mana yang stoknya belum habis atau tanggalnya belum kedaluwarsa.
        $spareparts->whereHas('purchaseOrderItems', function ($query) {
            // Kita ingin menampilkan sparepart jika ada SETIDAKNYA SATU batch yang masih valid.
            $query->whereRaw('quantity - sold_quantity > 0') // Stok tersisa
                  ->orWhere('expired_date', '>', Carbon::now()); // Tanggal kedaluwarsa belum lewat
        })
        // Tambahkan kondisi untuk sparepart yang belum memiliki item pembelian (stok 0) agar tetap tampil.
        ->orWhereDoesntHave('purchaseOrderItems');

        // Lakukan paginasi pada hasil query.
        $spareparts = $spareparts->paginate(10);

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
            'name' => 'required|string|max:255|unique:spareparts,name',
            'code_part' => 'nullable|string|unique:spareparts,code_part|max:255',
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

        // Get category name
        $category = Category::find($validatedData['category_id']);
        $categoryName = $category ? $category->name : '';

        // Generate code_part on the server
        $namePart = strtoupper(substr($validatedData['name'], 0, 3));
        $catPart = strtoupper(substr($categoryName, 0, 3));
        $random = mt_rand(100, 9999);

        $generatedCodePart = "{$catPart}-{$namePart}-{$random}";

        // Ensure uniqueness (add a loop to regenerate if it exists)
        while (Sparepart::where('code_part', $generatedCodePart)->exists()) {
            $random = mt_rand(100, 9999);
            $generatedCodePart = "{$catPart}-{$namePart}-{$random}";
        }

        $sparepart = Sparepart::create(array_merge($validatedData, [
            'code_part' => $generatedCodePart,
        ]));

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