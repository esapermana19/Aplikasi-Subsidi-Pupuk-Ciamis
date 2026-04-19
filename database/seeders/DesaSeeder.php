<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kecamatan;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
        // Fungsi pembantu untuk ambil ID berdasarkan nama kecamatan
        $getKecId = function($nama) {
            return DB::table('tabel_kecamatan')->where('nama_kecamatan', $nama)->value('id_kecamatan');
        };

        $desa = [
            // Kecamatan Ciamis
            ['id_kecamatan' => $getKecId('Ciamis'), 'nama_desa' => 'Benteng'],
            ['id_kecamatan' => $getKecId('Ciamis'), 'nama_desa' => 'Ciamis'],
            ['id_kecamatan' => $getKecId('Ciamis'), 'nama_desa' => 'Maleber'],
            ['id_kecamatan' => $getKecId('Ciamis'), 'nama_desa' => 'Sindangrasa'],

            // Kecamatan Cijeungjing
            ['id_kecamatan' => $getKecId('Cijeungjing'), 'nama_desa' => 'Cijeungjing'],
            ['id_kecamatan' => $getKecId('Cijeungjing'), 'nama_desa' => 'Dewasari'],
            ['id_kecamatan' => $getKecId('Cijeungjing'), 'nama_desa' => 'Kertabumi'],

            // Kecamatan Kawali
            ['id_kecamatan' => $getKecId('Kawali'), 'nama_desa' => 'Kawali'],
            ['id_kecamatan' => $getKecId('Kawali'), 'nama_desa' => 'Kawali Mukti'],
            ['id_kecamatan' => $getKecId('Kawali'), 'nama_desa' => 'Winduraja'],

            // Kecamatan Banjarsari
            ['id_kecamatan' => $getKecId('Banjarsari'), 'nama_desa' => 'Banjarsari'],
            ['id_kecamatan' => $getKecId('Banjarsari'), 'nama_desa' => 'Cikaso'],
            ['id_kecamatan' => $getKecId('Banjarsari'), 'nama_desa' => 'Purwasari'],

            // Kecamatan Sadananya
            ['id_kecamatan' => $getKecId('Sadananya'), 'nama_desa' => 'Sadananya'],
            ['id_kecamatan' => $getKecId('Sadananya'), 'nama_desa' => 'Mekarjadi'],
            ['id_kecamatan' => $getKecId('Sadananya'), 'nama_desa' => 'Tanjungsari'],
        ];

        DB::table('tabel_desa')->insert($desa);
    }
}
