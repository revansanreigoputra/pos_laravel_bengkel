<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'budi@example.com',
                'address' => 'Jl. Merdeka No. 10, Bandung'
            ],
            [
                'name' => 'Siti Aminah',
                'phone' => '082112345678',
                'email' => 'siti@example.com',
                'address' => 'Jl. Diponegoro No. 20, Jakarta'
            ],
            [
                'name' => 'Andi Pratama',
                'phone' => '085612345678',
                'email' => null,
                'address' => 'Jl. Sudirman No. 5, Surabaya'
            ],
            [
                'name' => 'Dewi Lestari',
                'phone' => '081298765432',
                'email' => 'dewi@example.com',
                'address' => 'Jl. Ahmad Yani No. 15, Yogyakarta'
            ],
            [
                'name' => 'Rudi Hartono',
                'phone' => '089998877665',
                'email' => 'rudi@example.com',
                'address' => 'Jl. Cempaka No. 7, Semarang'
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->insert(array_merge($customer, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
