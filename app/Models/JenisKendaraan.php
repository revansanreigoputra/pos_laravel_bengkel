<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKendaraan extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
