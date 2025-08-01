<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua suku cadang.
     */
    public function index()
    {
        // Mengambil semua sparepart dengan relasi kategori secara eager loading.
        // Menambahkan withSum untuk 'quantity' dan withAvg untuk 'purchase_price'
        // dari relasi purchaseOrderItems untuk menghitung total stok dan harga beli rata-rata.
        $spareparts = Sparepart::with('category')
            ->withSum('purchaseOrderItems', 'quantity')
            ->withAvg('purchaseOrderItems', 'purchase_price')
            ->latest()
            ->paginate(10);

        return view('pages.spareparts.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan formulir untuk membuat suku cadang baru.
     */
    public function create()
    {
        // Mengambil semua kategori untuk ditampilkan di dropdown formulir.
        $categories = Category::all();
        return view('pages.spareparts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan suku cadang baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk dari request.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => 'required|string|unique:spareparts,code_part|max:255',
            'selling_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
        ]);

        // Membuat entri Sparepart baru di database.
        Sparepart::create($validatedData);

        // Mengarahkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     * Menampilkan detail suku cadang tertentu.
     */
    public function show(Sparepart $sparepart)
    {
        // Memuat relasi category untuk ditampilkan di detail.
        $sparepart->load('category');
        return view('pages.spareparts.show', compact('sparepart'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan formulir untuk mengedit suku cadang tertentu.
     */
    public function edit(Sparepart $sparepart)
    {
        // Mengambil semua kategori untuk ditampilkan di dropdown formulir edit.
        $categories = Category::all();
        return view('pages.spareparts.edit', compact('sparepart', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * Memperbarui suku cadang tertentu di database.
     */
    public function update(Request $request, Sparepart $sparepart)
    {
        // Validasi data yang masuk untuk pembaruan.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code_part' => [
                'required',
                'string',
                Rule::unique('spareparts')->ignore($sparepart->id),
                'max:255',
            ],
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date',
        ]);

        // Memperbarui entri Sparepart di database.
        $sparepart->update($validatedData);

        // Mengarahkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus suku cadang tertentu dari database.
     */
    public function destroy(Sparepart $sparepart)
    {
        // Menghapus entri Sparepart dari database.
        $sparepart->delete();
        // Mengarahkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus!');
    }
}