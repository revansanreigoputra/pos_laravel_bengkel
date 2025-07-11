<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'nama_barang',
        'tipe_barang',
        'jumlah',
        'harga',
        'tanggal_masuk',
    ];
}
