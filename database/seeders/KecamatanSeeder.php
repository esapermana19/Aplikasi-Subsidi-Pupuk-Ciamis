<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = [
            ['nama_kecamatan' => 'Banjarsari'],
            ['nama_kecamatan' => 'Baregbeg'],
            ['nama_kecamatan' => 'Ciamis'],
            ['nama_kecamatan' => 'Cidolog'],
            ['nama_kecamatan' => 'Cihaurbeuti'],
            ['nama_kecamatan' => 'Cijeungjing'],
            ['nama_kecamatan' => 'Cikoneng'],
            ['nama_kecamatan' => 'Cimaragas'],
            ['nama_kecamatan' => 'Cipaku'],
            ['nama_kecamatan' => 'Cisaga'],
            ['nama_kecamatan' => 'Jatinagara'],
            ['nama_kecamatan' => 'Kawali'],
            ['nama_kecamatan' => 'Lakbok'],
            ['nama_kecamatan' => 'Lumbung'],
            ['nama_kecamatan' => 'Pamarican'],
            ['nama_kecamatan' => 'Panawangan'],
            ['nama_kecamatan' => 'Panjalu'],
            ['nama_kecamatan' => 'Panumbangan'],
            ['nama_kecamatan' => 'Purwadadi'],
            ['nama_kecamatan' => 'Rajadesa'],
            ['nama_kecamatan' => 'Rancah'],
            ['nama_kecamatan' => 'Sadananya'],
            ['nama_kecamatan' => 'Sindangkasih'],
            ['nama_kecamatan' => 'Sukadana'],
            ['nama_kecamatan' => 'Sukamantri'],
            ['nama_kecamatan' => 'Tambaksari'],
            ['nama_kecamatan' => 'Tidamar'],
        ];

        // Menggunakan DB table agar lebih cepat dan tidak terikat logic model
        DB::table('tabel_kecamatan')->insert($kecamatan);
    }
}
