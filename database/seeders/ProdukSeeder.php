<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        $produkFiles = [
            // Cincin
            'Cincin Constant.png',
            'Cincin Purpozs.jpg',
            'Cincin Timeless.jpeg',
            'Cincin Vste.png',
            'Cincing Glriosa.png',
            // Gelang
            'Gelang Butterflyz.jpg',
            'Gelang Lunnatezz Simely.png',
            'Gelang Pannce.png',
            'Gelang Xwaq.png',
            // Kalung
            'Kalung All Time Runner.jpeg',
            'Kalung Fosko.png',
            'Kalung Labirinth.png',
            'Kalung Rubyz.png',
            'Kalung Zpetz Gold.jpg',
        ];

        $deskripsiProduk = [
            'Constant' => 'Cincin Constant, simple tapi classy, cocok buat kamu yang suka tampil effortless tapi tetap stand out di tongkrongan!',
            'Purpozs' => 'Cincin Purpozs, desainnya unik banget, bikin jari kamu jadi pusat perhatian. Wajib punya buat yang suka vibes artsy!',
            'Timeless' => 'Cincin Timeless, klasik tapi nggak pernah ngebosenin. Style-nya abadi, cocok buat daily look atau special moment.',
            'Vste' => 'Cincin Vste, minimalis tapi kece. Pas banget buat kamu yang suka gaya clean dan modern.',
            'Glriosa' => 'Cincin Glriosa, sparkling maksimal! Bikin tangan kamu makin glowing, siap-siap jadi spotlight di setiap acara.',
            'Butterflyz' => 'Gelang Butterflyz, gemesin banget dengan sentuhan butterfly vibes. Bikin outfit kamu makin playful dan fresh!',
            'Lunnatezz Simely' => 'Gelang Lunnatezz Simely, desainnya cheerful, cocok buat kamu yang selalu happy dan suka spread positive energy.',
            'Pannce' => 'Gelang Pannce, bold dan edgy, pas buat kamu yang suka tampil beda dan anti mainstream.',
            'Xwaq' => 'Gelang Xwaq, simple tapi punya karakter kuat. Bikin look kamu makin cool dan confident.',
            'All Time Runner' => 'Kalung All Time Runner, timeless piece yang cocok buat semua gaya. Bikin leher kamu makin on point!',
            'Fosko' => 'Kalung Fosko, desainnya misterius dan elegan. Pas banget buat kamu yang suka aura calm tapi tetap menarik.',
            'Labirinth' => 'Kalung Labirinth, motifnya unik banget, cocok buat kamu yang suka eksplorasi gaya dan nggak takut tampil beda.',
            'Rubyz' => 'Kalung Rubyz, warnanya vibrant, bikin mood kamu auto naik setiap hari!',
            'Zpetz Gold' => 'Kalung Zpetz Gold, gold vibes-nya mewah tapi tetap fun. Pas buat party atau hangout bareng bestie!',
        ];

        foreach ($produkFiles as $file) {
            // Tentukan kategori_id
            if (Str::startsWith($file, 'Cincin')) {
                $kategori_id = 1;
            } elseif (Str::startsWith($file, 'Gelang')) {
                $kategori_id = 2;
            } elseif (Str::startsWith($file, 'Kalung')) {
                $kategori_id = 3;
            } else {
                $kategori_id = null;
            }

            // Ambil nama produk tanpa awalan kategori dan tanpa ekstensi
            $namaProduk = preg_replace('/^(Cincin|Cincing|Gelang|Kalung) /i', '', pathinfo($file, PATHINFO_FILENAME));

            // Harga random bulat 150000-300000
            $harga = rand(150, 300) * 1000;

            // Deskripsi unik
            $deskripsi = $deskripsiProduk[$namaProduk] ?? 'Aksesoris kekinian yang siap bikin penampilan kamu makin kece!';

            Produk::create([
                'kategori_id' => $kategori_id,
                'nama' => $namaProduk,
                'deskripsi' => $deskripsi,
                'harga' => $harga,
                'stok' => 50,
                'gambar' => $file,
            ]);
        }
    }
} 