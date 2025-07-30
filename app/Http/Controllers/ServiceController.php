<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\JenisKendaraan;

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
        $services = Service::all(); // You might not need $services for a new create form
        return view('pages.service.modal-create', compact('services', 'jenisKendaraans'));
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

        Service::create([
            'nama' => $request->nama,
            'jenis_kendaraan_id' => $request->jenis_kendaraan_id,
            'durasi_estimasi' => $request->durasi_estimasi,
            'harga_standar' => $request->harga_standar,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('service.index')->with('success', 'Service berhasil ditambahkan.');
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

        $service->update([
            'nama' => $request->nama,
            'jenis_kendaraan_id' => $request->jenis_kendaraan_id,
            'durasi_estimasi' => $request->durasi_estimasi,
            'harga_standar' => $request->harga_standar,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('service.index')->with('success', 'Service berhasil diperbarui.');
    }

    /**
     * Hapus data dari database.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('service.index')->with('success', 'Service berhasil dihapus.');
    }
}
