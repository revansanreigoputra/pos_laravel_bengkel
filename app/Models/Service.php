<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_kendaraan_id',
        'durasi_estimasi',
        'harga_standar',
        'status',
        'deskripsi',
    ];

    /**
     * Relasi ke model JenisKendaraan
     */
    public function jenisKendaraan()
    {
        return $this->belongsTo(JenisKendaraan::class);
    }
}
