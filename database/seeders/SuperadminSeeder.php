<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            User::create([
                'email' => 'superadmin@gmail.com',
                'nama' => 'Administrator Utama',
                'nip' => '198705082026051001',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'status_akun' => 'aktif',
                'no_hp' => '081234567890',
            ]);
        });

        $this->command->info('Superadmin seeder finished successfully.');
        $this->command->info('Email: superadmin@gmail.com');
        $this->command->info('Password: password');
    }
}
