<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sparepart;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\SparepartExport;
use App\Imports\SparepartImport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Exports\SparepartTemplate;

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
            'purchaseOrderItems' => function ($query) {
                $query->latest();
            }
        ]);

        // Filter berdasarkan kategori jika dipilih
        if ($request->has('category_id') && $request->category_id != '') {
            $spareparts->where('category_id', $request->category_id);
        }

        // Filter pencarian
        if ($request->has('search') && !empty($request->search)) {
            $spareparts->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter untuk hanya menampilkan sparepart dengan stok > 0
        $spareparts->whereHas('purchaseOrderItems', function ($q) {
            $q->selectRaw('sparepart_id, SUM(quantity - sold_quantity) as available_stock')
              ->groupBy('sparepart_id')
              ->havingRaw('SUM(quantity - sold_quantity) > 0');
        })->orWhereDoesntHave('purchaseOrderItems');
        
        // Tambahan filter untuk memastikan sparepart dengan purchase_order_items yang valid saja
        $spareparts->where(function ($query) {
            $query->whereHas('purchaseOrderItems', function ($q) {
                $q->where(function ($q2) {
                    $q2->whereRaw('quantity - sold_quantity > 0')
                       ->where(function ($q3) {
                           $q3->whereNull('expired_date')
                              ->orWhere('expired_date', '>', Carbon::now());
                       });
                });
            })->orWhereDoesntHave('purchaseOrderItems');
        });

        // Urutkan dan paginasi
        $spareparts = $spareparts->orderBy('name')->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('pages.spareparts.index', compact('spareparts', 'categories'));
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

    public function show(Sparepart $sparepart)
    {
        return view('pages.spareparts.show', compact('sparepart'));
    }
    /**
     * Remove the specified resource from storage.
     * Menghapus sumber daya yang ditentukan dari penyimpanan.
     */
   public function destroy(Sparepart $sparepart)
    {
        try {
            // 1. Cek penggunaan di transaksi pembelian (purchase orders) 
            if ($sparepart->purchaseOrderItems()->count() > 0) {
                return redirect()->back()->withErrors('Sparepart tidak dapat dihapus karena sudah memiliki transaksi pembelian.');
            }

            // 2. Cek penggunaan di transaksi penjualan 
            if ($sparepart->transactionItems()->exists()) {
                return redirect()->back()->withErrors('Sparepart tidak dapat dihapus karena sudah memiliki transaksi penjualan.');
            }

            // 3. Jika tidak ada di transaksi, lanjutkan proses hapus
            DB::beginTransaction();
            $sparepart->delete();
            DB::commit();

           
            return redirect()->back()->withSuccess('Data Sparepart berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
           return redirect()->back()->withErrors('Gagal menghapus supplier.');
        }
    }
    // export import and template function
    /**
     * Download an Excel template for bulk sparepart uploads.
     */
    public function downloadTemplate()
    {
        return Excel::download(new SparepartTemplate, 'sparepart_template.xlsx');
    }
    /**
     * Import spareparts from an Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Pass the import class to the Excel facade.
            Excel::import(new SparepartImport, $request->file('file'));
            return redirect()->route('spareparts.index')->with('success', 'Spareparts imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('spareparts.index')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export all spareparts to an Excel file.
     */
    public function export()
    {
        return Excel::download(new SparepartExport, 'data_sparepart.xlsx');
    }
}