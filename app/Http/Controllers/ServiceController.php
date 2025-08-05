<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\JenisKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade untuk transaksi
use Illuminate\Support\Facades\Log; // Import Log facade untuk logging error
use Exception;

class ServiceController extends Controller
{
    /**
     * Tampilkan semua data service.
     */
    public function index()
    {
        $services = Service::with('jenisKendaraan')->get();
        $jenisKendaraans = JenisKendaraan::all();

        return view('pages.service.index', compact('services', 'jenisKendaraans'));
    }

    public function create()
    {
        $jenisKendaraans = JenisKendaraan::all();
        return view('pages.service.modal-create', compact('jenisKendaraans'));
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kendaraan_id' => 'required|exists:jenis_kendaraans,id',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            Service::create($request->all());
            return redirect()->route('service.index')->with('success', 'Service berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Error storing service: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan service: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan data untuk edit.
     */
    public function edit(Service $service)
    {
        $jenisKendaraans = JenisKendaraan::all();
        return view('pages.service.modal-edit', compact('service', 'jenisKendaraans'));
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kendaraan_id' => 'required|exists:jenis_kendaraans,id',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $service->update($request->all());
            return redirect()->route('service.index')->with('success', 'Service berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('Error updating service: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui service: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus data dari database.
     * Menambahkan validasi untuk mencegah penghapusan service yang sudah terpakai.
     */
    public function destroy(Service $service)
    {
        DB::beginTransaction();
        try {
            // Periksa apakah service ini sudah pernah digunakan dalam transaksi
            if ($service->transactionItems()->exists()) {
                throw new Exception('Tidak dapat menghapus service yang sudah digunakan dalam transaksi.');
            }
            
            $service->delete();
            DB::commit();
            return redirect()->route('service.index')->with('success', 'Service berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting service: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus service: ' . $e->getMessage());
        }
    }
}