<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class KirimPengingat extends Command
{
    protected $signature = 'reminders:send {--tanpa-email : Hanya buat notifikasi web, tanpa kirim email}';

    protected $description = 'Hitung sisa waktu semua tugas mahasiswa dan kirim pengingat deadline (H-3, H-1, jam, terlambat)';

    public function handle(ReminderService $service): int
    {
        $jumlah = $service->prosesSemua(! $this->option('tanpa-email'));

        $this->info("🍓 Selesai. {$jumlah} pengingat baru dibuat.");

        return self::SUCCESS;
    }
}
