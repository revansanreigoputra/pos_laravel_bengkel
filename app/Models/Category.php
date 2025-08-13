<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all spareparts associated with this category.
     */
    public function spareparts()
    {
        return $this->hasMany(Sparepart::class);
    }

    /**
     * Check if this category has any spareparts associated.
     */
    public function hasSpareparts(): bool
    {
        return $this->spareparts()->exists();
    }
}