<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
    /**
     * Menghapus data jenis kendaraan.
     */
     public function destroy(JenisKendaraan $jenisKendaraan)
    {
        try {
            // Cek apakah jenis kendaraan ini sudah digunakan di tabel services.
            if ($jenisKendaraan->services()->exists()) {
                return redirect()->back()->withErrors('Jenis kendaraan tidak dapat dihapus karena sudah digunakan pada data service.');
            }

            // Jika tidak ada relasi, lanjutkan proses penghapusan
            DB::beginTransaction();
            $jenisKendaraan->delete();
            DB::commit();

            return redirect()->back()->withSuccess('Data Jenis Kendaraan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Gagal menghapus jenis kendaraan: ' . $e->getMessage());
        }
    }
}
