<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $pesanans = Pesanan::all();

        if ($pesanans->count() > 0) {
            foreach ($pesanans as $pesanan) {
                // Buat pembayaran untuk setiap pesanan
                $statusBayar = ['pending', 'sukses', 'gagal'][rand(0, 2)];
                
                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'tanggal_bayar' => Carbon::now()->subDays(rand(1, 30)),
                    'jumlah_bayar' => $pesanan->total_harga,
                    'status_bayar' => $statusBayar,
                    'bukti_bayar' => $statusBayar == 'sukses' ? 'uploads/bukti/sample.jpg' : null
                ]);
            }
        }
    }
} 