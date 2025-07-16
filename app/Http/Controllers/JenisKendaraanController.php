<?php

namespace App\Http\Controllers;

use App\Models\JenisKendaraan;
use Illuminate\Http\Request;

class JenisKendaraanController extends Controller
{
    /**
     * Menampilkan semua data jenis kendaraan.
     */
    public function index()
    {
        $jenisKendaraans = JenisKendaraan::all();
        return view('pages.jenis_kendaraan.index', compact('jenisKendaraans'));
    }

    /**
     * Menyimpan data jenis kendaraan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:jenis_kendaraans,nama',
        ]);

        JenisKendaraan::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('jenis-kendaraan.index')->with('success', 'Jenis kendaraan berhasil ditambahkan.');
    }

    /**
     * Menampilkan data untuk form edit (jika pakai modal AJAX).
     */
    public function edit(JenisKendaraan $jenisKendaraan)
    {
        return response()->json($jenisKendaraan);
    }

    /**
     * Mengupdate data jenis kendaraan.
     */
    public function update(Request $request, JenisKendaraan $jenisKendaraan)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:jenis_kendaraans,nama,' . $jenisKendaraan->id,
        ]);

        $jenisKendaraan->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('jenis-kendaraan.index')->with('success', 'Jenis kendaraan berhasil diperbarui.');
    }

    /**
     * Menghapus data jenis kendaraan.
     */
    public function destroy(JenisKendaraan $jenisKendaraan)
    {
        $jenisKendaraan->delete();
        return redirect()->route('jenis-kendaraan.index')->with('success', 'Jenis kendaraan berhasil dihapus.');
    }
}
