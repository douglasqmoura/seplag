<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Seplag',
            'email' => 'admin@seplag.mt.gov.br',
            'password' => Hash::make('senha123'),
            'admin' => true,
        ]);

        User::create([
            'name' => 'Convidado Seplag',
            'email' => 'guest@seplag.mt.gov.br',
            'password' => Hash::make('senha123'),
        ]);
    }
}
