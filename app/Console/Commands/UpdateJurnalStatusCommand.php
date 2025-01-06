<?php

namespace App\Console\Commands;

use App\Models\Penjualan\SalesOrder;
use Illuminate\Console\Command;

class UpdateJurnalStatusCommand extends Command
{
    protected $signature = 'jurnal:update-status';
    protected $description = 'Update status jurnal yang masih review menjadi settle jika belum ada penanganan pada hari berikutnya';

    public function handle()
    {
        $tanggalKemarin = now()->subDay()->startOfDay()->toDateString(); // Mendapatkan tanggal kemarin

        // Ambil semua jurnal yang masih "review" dan belum ada penanganan sampai kemarin
        $jurnals = SalesOrder::where('status', '2')
                         ->whereDate('tanggal_order', $tanggalKemarin)
                         ->get();

        foreach ($jurnals as $jurnal) {
            // Ubah status jurnal menjadi "settle"
            $jurnal->status = '3';
            $jurnal->settel_by = '1'; // Setel by AUTO
            $jurnal->save();

            // Opsional: Log perubahan
            \Log::info("Jurnal ID {$jurnal->id} status diubah menjadi 'settle'.");
        }

        $this->info('Status jurnal berhasil diupdate.');
    }
}
