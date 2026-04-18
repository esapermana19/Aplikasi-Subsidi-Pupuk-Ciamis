<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;

class AdminProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user berdasarkan email yang sudah dibuat di UserSeeder
        $user = User::where('email', 'admin@asupciamis.com')->first();

        if ($user) {
            Admin::updateOrCreate(
                ['id_user' => $user->id_user], // Pastikan model User sudah pakai protected $primaryKey = 'id_user'
                [
                    'nip'        => '198001012024041001',
                    'nama_admin' => 'Administrator Utama',
                    'last_login' => now(),
                ]
            );
            $this->command->info('Profil Admin berhasil dibuat.');
        } else {
            $this->command->error('Gagal: Akun User tidak ditemukan. Jalankan UserSeeder dulu!');
        }
    }
}