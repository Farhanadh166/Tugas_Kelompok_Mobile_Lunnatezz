<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Produk;
use App\Models\Kategori;
use Carbon\Carbon;

class ContohTransaksiSeeder extends Seeder
{
    public function run()
    {
        // Pastikan kategori ada
        $kategori = Kategori::firstOrCreate([
            'id' => 1
        ], [
            'nama' => 'Aksesoris Wanita',
            'deskripsi' => 'Kategori aksesoris wanita',
        ]);

        // Pastikan user dan produk ada
        $user = User::firstOrCreate([
            'id' => 5
        ], [
            'nama' => 'Contoh Pelanggan',
            'email' => 'pelanggan5@example.com',
            'password' => bcrypt('password'),
            'peran' => 'pelanggan',
        ]);

        $produk1 = Produk::firstOrCreate(['id' => 2], [
            'nama' => 'Gelang Cantik',
            'kategori_id' => 1,
            'harga' => 50000,
            'stok' => 100,
        ]);
        $produk2 = Produk::firstOrCreate(['id' => 4], [
            'nama' => 'Kalung Mewah',
            'kategori_id' => 1,
            'harga' => 50000,
            'stok' => 100,
        ]);

        // 1. Keranjang Aktif
        $keranjang = Keranjang::create([
            'user_id' => $user->id,
            'created_at' => Carbon::parse('2024-07-06 10:00:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:00:00'),
        ]);
        ItemKeranjang::create([
            'keranjang_id' => $keranjang->id,
            'produk_id' => $produk1->id,
            'jumlah' => 1,
            'created_at' => Carbon::parse('2024-07-06 10:01:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:01:00'),
        ]);
        ItemKeranjang::create([
            'keranjang_id' => $keranjang->id,
            'produk_id' => $produk2->id,
            'jumlah' => 2,
            'created_at' => Carbon::parse('2024-07-06 10:02:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:02:00'),
        ]);

        // 2. Pesanan
        $pesanan = Pesanan::create([
            'user_id' => $user->id,
            'tanggal_pesanan' => Carbon::parse('2024-07-06 10:10:00'),
            'total_harga' => 150000,
            'status' => 'pending',
            'alamat_kirim' => 'Jl. Mawar No. 1',
            'created_at' => Carbon::parse('2024-07-06 10:10:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:10:00'),
        ]);
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk1->id,
            'harga' => 50000,
            'jumlah' => 1,
            'created_at' => Carbon::parse('2024-07-06 10:10:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:10:00'),
        ]);
        DetailPesanan::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk2->id,
            'harga' => 50000,
            'jumlah' => 2,
            'created_at' => Carbon::parse('2024-07-06 10:10:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:10:00'),
        ]);

        // 3. Pembayaran
        Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'tanggal_bayar' => Carbon::parse('2024-07-06 10:15:00'),
            'jumlah_bayar' => 150000,
            'status_bayar' => 'sukses',
            'bukti_bayar' => 'uploads/bukti/bukti1.jpg',
            'created_at' => Carbon::parse('2024-07-06 10:15:00'),
            'updated_at' => Carbon::parse('2024-07-06 10:15:00'),
        ]);
    }
} 