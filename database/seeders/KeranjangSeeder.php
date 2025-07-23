<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\User;
use App\Models\Produk;

class KeranjangSeeder extends Seeder
{
    public function run()
    {
        // Ambil user pelanggan (bukan admin)
        $users = User::where('peran', 'pelanggan')->get();
        $produks = Produk::all();

        if ($users->count() > 0 && $produks->count() > 0) {
            foreach ($users as $user) {
                // Buat keranjang untuk setiap user
                $keranjang = Keranjang::create([
                    'user_id' => $user->id
                ]);

                // Tambahkan 1-3 item ke keranjang
                $jumlahItem = rand(1, 3);
                for ($i = 0; $i < $jumlahItem; $i++) {
                    $produk = $produks->random();
                    ItemKeranjang::create([
                        'keranjang_id' => $keranjang->id,
                        'produk_id' => $produk->id,
                        'jumlah' => rand(1, 3)
                    ]);
                }
            }
        }
    }
} 