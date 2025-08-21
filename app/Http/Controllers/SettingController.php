<?php

namespace App\Http\Controllers;

use App\Models\BengkelSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan
     */
    public function index()
    {
        // Ambil data pengaturan pertama (karena cuma ada satu row)
        $settings = BengkelSetting::first();

        return view('pages.settings.index', compact('settings'));
    }

    /**
     * Update data pengaturan
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'alamat_bengkel' => 'nullable|string',
            'telepon_bengkel' => 'nullable|string|max:20',
            'email_bengkel' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Ambil atau buat data settings
        $settings = BengkelSetting::firstOrNew(['id' => 1]);

        $settings->nama_bengkel   = $request->nama_bengkel;
        $settings->alamat_bengkel = $request->alamat_bengkel;
        $settings->telepon_bengkel = $request->telepon_bengkel;
        $settings->email_bengkel  = $request->email_bengkel;

        // Handle upload logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            // Simpan logo baru
            $path = $request->file('logo')->store('logos', 'public');
            $settings->logo_path = $path;
        }

        $settings->save();

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}