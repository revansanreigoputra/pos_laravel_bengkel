<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mesin',
            'Sistem Transmisi',
            'Body dan Aksesoris',
            'Oli dan Cairan',
            'Ban dan Velg',
            'Sistem Pendingin',
            'Sistem Bahan Bakar ',
            'Sistem Kelistrikan',
            'Sistem Rem',
            'Suspensi dan Kaki Kaki'
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
