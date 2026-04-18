<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@asupciamis.com'],
            [
                'password'    => Hash::make('admin123'),
                'role'        => 'Admin',
                'status_akun' => 'aktif',
            ]
        );
        
        $this->command->info('User login berhasil dibuat.');
    }
}