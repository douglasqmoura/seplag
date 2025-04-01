<?php

namespace Database\Seeders;

use App\Models\ServidorTemporario;
use Illuminate\Database\Seeder;

class ServidorTemporarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServidorTemporario::create([
            'pes_id' => 2,
            'st_data_admissao' => '2024-01-01',
            'st_data_demissao' => '2024-12-31',
        ]);
    }
}
