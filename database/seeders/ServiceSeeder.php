<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID jenis kendaraan berdasarkan nama
        $motorMatic = DB::table('jenis_kendaraans')->where('nama', 'motor matic')->value('id');
        $motorManual = DB::table('jenis_kendaraans')->where('nama', 'motor manual')->value('id');
        $mobilMatic = DB::table('jenis_kendaraans')->where('nama', 'mobil matic')->value('id');
        $mobilManual = DB::table('jenis_kendaraans')->where('nama', 'mobil manual')->value('id');

        // Data service
        $services = [
            [
                'nama' => 'Ganti Oli Motor Matic',
                'jenis_kendaraan_id' => $motorMatic,
                'durasi_estimasi' => '30 menit',
                'harga_standar' => 25000,
                'status' => 'aktif',
                'deskripsi' => 'Penggantian oli mesin untuk motor matic',
            ],
            [
                'nama' => 'Service Rem Mobil Manual',
                'jenis_kendaraan_id' => $mobilManual,
                'durasi_estimasi' => '1 jam',
                'harga_standar' => 120000,
                'status' => 'aktif',
                'deskripsi' => 'Pengecekan dan penggantian kampas rem',
            ],
            [
                'nama' => 'Tune Up Motor Manual',
                'jenis_kendaraan_id' => $motorManual,
                'durasi_estimasi' => '45 menit',
                'harga_standar' => 50000,
                'status' => 'aktif',
                'deskripsi' => 'Perawatan mesin motor manual',
            ],
            [
                'nama' => 'Ganti Oli Mobil Matic',
                'jenis_kendaraan_id' => $mobilMatic,
                'durasi_estimasi' => '1 jam',
                'harga_standar' => 100000,
                'status' => 'aktif',
                'deskripsi' => 'Penggantian oli transmisi mobil matic',
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert(array_merge($service, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
