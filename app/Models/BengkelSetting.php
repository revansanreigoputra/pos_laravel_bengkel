<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BengkelSetting extends Model
{
    protected $table = 'bengkel_settings';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'nama_bengkel',
        'alamat_bengkel',
        'telepon_bengkel',
        'email_bengkel',
        'logo_path',
    ];

    // Kalau tidak ada timestamps di tabel
    public $timestamps = false;

    /**
     * Helper untuk ambil data settings
     */
    public static function getSettings()
    {
        return self::first();
    }

    /**
     * Helper untuk ambil logo (langsung URL lengkap)
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return asset('images/default-logo.png'); // fallback kalau belum ada logo
    }

    /**
     * Helper untuk ambil path logo untuk PDF
     */
    public function getLogoPathForPdfAttribute()
    {
        if ($this->logo_path) {
            return public_path('storage/' . $this->logo_path);
        }
        return public_path('images/default-logo.png');
    }
}