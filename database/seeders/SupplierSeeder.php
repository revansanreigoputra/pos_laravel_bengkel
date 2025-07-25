<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Teguh Prima',
                'phone' => '081234567890',
                'email' => 'maju@example.com',
                'address' => 'Jl. Jendral Sudirman No. 123, Bandung',
                'note' => 'CV Maju Motor'
            ],
            [
                'name' => 'Nafarudin Pamungkas',
                'phone' => '082112345678',
                'email' => 'sparepartjaya@example.com',
                'address' => 'Jl. Gatot Subroto No. 45, Jakarta',
                'note' => 'PT Sparepart Jaya'
            ],
            [
                'name' => 'Bondan Gunawan Prakosa',
                'phone' => '085612345678',
                'email' => null,
                'address' => 'Jl. Imam Bonjol No. 7, Surabaya',
                'note' => 'UD Sumber Sukses'
            ],
            [
                'name' => 'Rizky Dwi Riswanto',
                'phone' => '081298765432',
                'email' => 'mitra@example.com',
                'address' => 'Jl. Diponegoro No. 9, Yogyakarta',
                'note' => 'CV Mitra Bengkel'
            ]
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert(array_merge($supplier, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
