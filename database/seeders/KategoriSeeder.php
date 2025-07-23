<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategoris = [
            [
                'nama' => 'Cincin',
                'deskripsi' => 'Cincin-cincin elegan yang memancarkan keindahan dan keanggunan di setiap jemari.'
            ],
            [
                'nama' => 'Gelang',
                'deskripsi' => 'Gelang eksklusif dengan desain modern, menambah pesona di setiap penampilan.'
            ],
            [
                'nama' => 'Kalung',
                'deskripsi' => 'Kalung menawan yang memperindah leher dan memancarkan aura kepercayaan diri.'
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::firstOrCreate(['nama' => $kategori['nama']], $kategori);
        }
    }
} 