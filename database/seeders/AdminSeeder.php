<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin ASUP',
            'email' => 'superadmin@asup.com',
            'nik_nip' => '3207000000000001', // Sesuaikan format NIK Ciamis
            'alamat' => 'Kantor Pusat Ciamis',
            'password' => 'superadmin123', // Akan di-hash otomatis jika di model sudah diset, jika tidak pakai bcrypt()
            'role' => 'superadmin',
            'status_akun' => 'aktif', // Admin langsung aktif
        ]);
        User::create([
            'name' => 'Admin ASUP',
            'email' => 'admin@asup.com',
            'nik_nip' => '3207000000000002', // Sesuaikan format NIK Ciamis
            'alamat' => 'Kantor Pusat Ciamis',
            'password' => 'admin123', // Akan di-hash otomatis jika di model sudah diset, jika tidak pakai bcrypt()
            'role' => 'admin',
            'status_akun' => 'aktif', // Admin langsung aktif
        ]);
    }
}
