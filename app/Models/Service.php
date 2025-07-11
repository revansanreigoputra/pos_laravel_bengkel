<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'durasi_estimasi',
        'harga_standar',
        'status',
        'deskripsi',
    ];
}
