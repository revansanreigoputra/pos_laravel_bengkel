<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Tampilkan semua data service.
     */
    public function index()
    {
        $services = Service::all();
        return view('pages.service.index', compact('services'));
    }

    /**
     * Simpan data baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ]);

        Service::create($request->all());

        return redirect()->route('service.index')->with('success', 'Service berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit (jika pakai modal bisa dihandle di blade).
     */
    public function edit(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Update data di database.
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'durasi_estimasi' => 'required|string|max:100',
            'harga_standar' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'deskripsi' => 'nullable|string',
        ]);

        $service->update($request->all());

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
