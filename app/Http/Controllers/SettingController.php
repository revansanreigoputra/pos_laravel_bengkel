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
        $settings = BengkelSetting::getSettings();
        return view('pages.settings.index', compact('settings'));
    }

    /**
     * Update pengaturan bengkel
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_bengkel' => 'required|string|max:255',
            'alamat_bengkel' => 'nullable|string|max:500',
            'telepon_bengkel' => 'nullable|string|max:50',
            'email_bengkel' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $settings = BengkelSetting::getSettings();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path) {
                Storage::delete('public/' . $settings->logo_path);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo_path = $logoPath;
        }

        // Update other settings
        $settings->update([
            'nama_bengkel' => $request->nama_bengkel,
            'alamat_bengkel' => $request->alamat_bengkel,
            'telepon_bengkel' => $request->telepon_bengkel,
            'email_bengkel' => $request->email_bengkel,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan bengkel berhasil diperbarui!');
    }
}