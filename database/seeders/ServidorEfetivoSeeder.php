<?php

namespace Database\Seeders;

use App\Models\ServidorEfetivo;
use Illuminate\Database\Seeder;

class ServidorEfetivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServidorEfetivo::create([
            'pes_id' => 1,
            'se_matricula' => '20250001',
        ]);
    }
}
