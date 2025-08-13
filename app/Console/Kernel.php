<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar Artisan commands yang tersedia.
     *
     * @var array
     */
    protected $commands = [
        // Kalau mau, bisa daftarkan command manual di sini
        // \App\Console\Commands\ScanStockAlerts::class,
    ];

    /**
     * Definisikan schedule command yang akan dijalankan otomatis.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan scan stok kedaluwarsa & stok habis tiap hari jam 07:00
        $schedule->command('stock:scan-alerts')->dailyAt('07:00');
    }

    /**
     * Daftar file command artisan.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}