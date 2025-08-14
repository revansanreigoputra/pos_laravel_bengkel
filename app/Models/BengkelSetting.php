<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BengkelSetting extends Model
{
    use HasFactory;

    protected $table = 'bengkel_settings';

    protected $fillable = [
        'nama_bengkel',
        'alamat_bengkel',
        'telepon_bengkel',
        'email_bengkel',
        'logo_path',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the full URL for the logo
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : asset('assets/logo.png');
    }

    /**
     * Get the default settings
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'nama_bengkel' => 'BengkelKu',
            'alamat_bengkel' => 'Jl. Contoh No. 123, Godean, Yogyakarta',
            'telepon_bengkel' => '0812-3456-7890',
            'email_bengkel' => 'info@bengkelku.com',
        ]);
    }
}