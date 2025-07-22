<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            'motor matic',
            'motor manual',
            'mobil matic',
            'mobil manual',
        ];

        foreach ($jenis as $j) {
            DB::table('jenis_kendaraans')->insert([
                'nama' => $j,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
