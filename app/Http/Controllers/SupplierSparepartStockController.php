<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\SupplierSparepartStock;
use App\Models\Supplier;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Menambahkan untuk menangani file
use Carbon\Carbon; // Digunakan untuk tanggal default jika tidak ada

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
        // Aturan validasi baru
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'spareparts' => 'required|array|min:1',
            'spareparts.*.sparepart_id' => 'required|exists:spareparts,id',
            'spareparts.*.quantity' => 'required|numeric|min:1',
            'spareparts.*.purchase_price' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            // Hapus rule 'unique' di sini karena Anda sudah menghapusnya di database
            'invoice_number' => 'nullable|string|max:255',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Tambah validasi untuk file invoice
            'note' => 'nullable|string',
        ]);

        $invoiceFilePath = null;
        // Upload file invoice jika ada
        if ($request->hasFile('invoice_file')) {
            $path = $request->file('invoice_file')->store('public/invoices'); // Simpan di storage/app/public/invoices
            $invoiceFilePath = Storage::url($path); // Dapatkan URL yang dapat diakses publik
        }

        foreach ($validated['spareparts'] as $sparepartInput) {
            $stock = SupplierSparepartStock::create([
                'supplier_id' => $validated['supplier_id'],
                'sparepart_id' => $sparepartInput['sparepart_id'],
                'quantity' => $sparepartInput['quantity'],
                'purchase_price' => $sparepartInput['purchase_price'],
                'received_date' => $validated['received_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,    // Simpan invoice_number
                'invoice_file_path' => $invoiceFilePath, // Simpan path file invoice
                'note' => $validated['note'] ?? null,
            ]);

            // --- BAGIAN BARU: Perbarui purchase_price di tabel sparepart ---
            // Asumsi: purchase_price sparepart di master data akan diperbarui
            // dengan harga beli dari item yang baru saja ditambahkan.
            // Jika ada beberapa item dalam satu invoice, harga beli akan diperbarui
            // berulang kali, dan yang terakhir akan menjadi nilai final.
            $sparepart = Sparepart::find($sparepartInput['sparepart_id']);
            if ($sparepart) {
                $sparepart->purchase_price = $sparepartInput['purchase_price'];
                $sparepart->save();
            }
            // --- AKHIR BAGIAN BARU ---
        }

        return redirect()->route('stock-handle.index')->with('success', 'Stock berhasil ditambahkan.');
    }


    public function edit(SupplierSparepartStock $stock)
    {
        $suppliers = Supplier::all();
        $spareparts = Sparepart::all();

        return view('pages.stock-handle.edit', compact('stock', 'suppliers', 'spareparts'));
    }

    public function update(Request $request, SupplierSparepartStock $stock)
    {
        // Ambil data sebelum update untuk perbandingan stok
        $oldQuantity = $stock->quantity;
        $oldSparepartId = $stock->sparepart_id;

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'received_date' => 'nullable|date',
            // Hapus unique rule atau pastikan mengabaikan record saat ini jika unique tetap ada
            // Karena Anda sudah menghapus unique constraint di DB, kita hapus juga di sini.
            'invoice_number' => 'nullable|string|max:255',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validasi file invoice
            'remove_invoice_file' => 'nullable|boolean', // Checkbox untuk menghapus file
            'note' => 'nullable|string',
        ]);

        $invoiceFilePath = $stock->invoice_file_path; // Ambil path yang sudah ada

        // Handle upload file baru atau penghapusan file lama
        if ($request->boolean('remove_invoice_file')) { // Jika checkbox hapus dicentang
            if ($invoiceFilePath) {
                // Hapus file dari storage
                Storage::delete(str_replace('/storage/', 'public/', $invoiceFilePath));
                $invoiceFilePath = null; // Set path menjadi null di DB
            }
        } elseif ($request->hasFile('invoice_file')) { // Jika ada file baru diunggah
            // Hapus file lama jika ada
            if ($invoiceFilePath) {
                Storage::delete(str_replace('/storage/', 'public/', $invoiceFilePath));
            }
            // Simpan file baru dan dapatkan URL-nya
            $path = $request->file('invoice_file')->store('public/invoices');
            $invoiceFilePath = Storage::url($path);
        }

        // Siapkan data untuk update
        $dataToUpdate = [
            'supplier_id' => $validated['supplier_id'],
            'sparepart_id' => $validated['sparepart_id'],
            'quantity' => $validated['quantity'],
            'purchase_price' => $validated['purchase_price'],
            'received_date' => $validated['received_date'],
            'invoice_number' => $validated['invoice_number'] ?? null,
            'invoice_file_path' => $invoiceFilePath,
            'note' => $validated['note'] ?? null,
        ];

        // Update stock record
        $stock->update($dataToUpdate);

        // --- BAGIAN BARU: Perbarui purchase_price di tabel sparepart ---
        // Kita juga perlu menyesuaikan stok_total jika sparepart_id atau quantity berubah
        // Asumsi Model Event `booted` method sudah menangani `syncSparepartQuantity`
        // Yang perlu kita lakukan di sini adalah memperbarui `purchase_price` di Sparepart yang relevan.

        $currentSparepart = Sparepart::find($stock->sparepart_id);
        if ($currentSparepart) {
            $currentSparepart->purchase_price = $stock->purchase_price; // Perbarui dengan harga beli dari stok ini
            $currentSparepart->save();
        }

        // --- AKHIR BAGIAN BARU ---

        return redirect()->route('stock-handle.index')->with('success', 'Stock berhasil diperbarui.');
    }

    public function destroy(SupplierSparepartStock $stock)
    {
        // Hapus file invoice jika ada
        if ($stock->invoice_file_path) {
            Storage::delete(str_replace('/storage/', 'public/', $stock->invoice_file_path));
        }

        // Dapatkan sparepart_id sebelum dihapus untuk pembaruan harga beli
        $sparepartId = $stock->sparepart_id;

        // Delete stock record (Model Event akan memicu syncSparepartQuantity untuk kuantitas)
        $stock->delete();

        // --- BAGIAN BARU: Perbarui purchase_price di tabel sparepart setelah penghapusan ---
        // Temukan sparepart yang terkait
        $sparepart = Sparepart::find($sparepartId);
        if ($sparepart) {
            // Setelah stok ini dihapus, kita perlu menentukan harga beli terbaru yang berlaku.
            // Ini bisa jadi harga beli dari entri stok terbaru lainnya untuk sparepart ini.
            $latestStock = SupplierSparepartStock::where('sparepart_id', $sparepartId)
                                                ->orderByDesc('received_date') // Urutkan berdasarkan tanggal terima terbaru
                                                ->orderByDesc('created_at')    // Jika tanggal sama, ambil yang terbaru dibuat
                                                ->first();

            $sparepart->purchase_price = $latestStock->purchase_price ?? 0; // Jika tidak ada stok tersisa, set 0
            $sparepart->save();
        }
        // --- AKHIR BAGIAN BARU ---

        return redirect()->route('stock-handle.index')->with('success', 'Stock berhasil dihapus.');
    }

    // Untuk download file invoice
    public function downloadInvoice(SupplierSparepartStock $stock)
    {
        if ($stock->invoice_file_path) {
            // Ubah kembali URL publik menjadi path internal storage
            $filePath = str_replace('/storage/', 'public/', $stock->invoice_file_path);

            if (Storage::exists($filePath)) {
                // Mendapatkan nama file asli untuk diunduh
                $fileName = basename($filePath);
                return Storage::download($filePath, $fileName);
            }
        }
        return redirect()->back()->with('error', 'File invoice tidak ditemukan.');
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