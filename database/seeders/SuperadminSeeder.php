<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Superadmin;
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
            $user = User::create([
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'status_akun' => 'aktif',
                'no_hp' => '081234567890',
            ]);

            Superadmin::create([
                'id_user' => $user->id_user,
                'nama_superadmin' => 'Administrator Utama',
                'nip' => '198705082026051001',
            ]);
        });

        $this->command->info('Superadmin seeder finished successfully.');
        $this->command->info('Email: superadmin@gmail.com');
        $this->command->info('Password: password');
    }
}
