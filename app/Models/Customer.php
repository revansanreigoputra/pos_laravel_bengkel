<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'address'];

    // Definisi relasi ke Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}