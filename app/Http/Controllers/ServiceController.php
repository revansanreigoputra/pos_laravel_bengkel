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
        $services = Service::with('jenisKendaraan')->orderBy('created_at', 'desc')->get();
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
            'nama' => 'required|string|max:255|unique:services,nama',
            'jenis_kendaraan_id' => 'required|exists:jenis_kendaraans,id',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.unique' => 'Nama service ini sudah ada. Mohon gunakan nama lain.',
            'nama.required' => 'Nama service harus diisi.',
            'jenis_kendaraan_id.required' => 'Jenis kendaraan harus dipilih.',
            'jenis_kendaraan_id.exists' => 'Jenis kendaraan yang dipilih tidak valid.',
            'durasi_estimasi.required' => 'Durasi estimasi harus diisi.',
            'harga_standar.required' => 'Harga standar harus diisi.',
            'harga_standar.numeric' => 'Harga standar harus berupa angka.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
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
            'nama' => 'required|string|max:255|unique:services,nama,' . $service->id,
            'jenis_kendaraan_id' => 'required|exists:jenis_kendaraans,id',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ], [
            'nama.unique' => 'Nama service ini sudah ada. Mohon gunakan nama lain.',
            'nama.required' => 'Nama service harus diisi.',
            'jenis_kendaraan_id.required' => 'Jenis kendaraan harus dipilih.',
            'jenis_kendaraan_id.exists' => 'Jenis kendaraan yang dipilih tidak valid.',
            'durasi_estimasi.required' => 'Durasi estimasi harus diisi.',
            'harga_standar.required' => 'Harga standar harus diisi.',
            'harga_standar.numeric' => 'Harga standar harus berupa angka.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
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
     */ public function destroy(Service $service)
    {
        try {
            // Periksa apakah service sudah digunakan dalam transaksi
            if ($service->transactionItems()->exists()) {
                return redirect()->back()->with('error', 'Service ini tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            }

            DB::beginTransaction();
            $service->delete();
            DB::commit();

            return redirect()->route('service.index')->with('success', 'Service berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting service: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mencoba menghapus service. Silakan coba lagi.');
        }
    }

    public function changeStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:aktif,nonaktif'
        ]);

        try {
            $service->update(['status' => $request->status]);
            return redirect()->route('service.index')->with('success', 'Status service berhasil diubah.');
        } catch (Exception $e) {
            Log::error('Error changing service status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status service: ' . $e->getMessage());
        }
    }
}
