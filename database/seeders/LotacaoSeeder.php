<?php

namespace Database\Seeders;

use App\Models\Lotacao;
use Illuminate\Database\Seeder;

class LotacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lotacao::create([
            'pes_id' => 1,
            'unid_id' => 1,
            'lot_data_lotacao' => now(),
            'lot_data_remocao' => null,
            'lot_portaria' => 'Portaria 123/2024',
        ]);
        Lotacao::create([
            'pes_id' => 2,
            'unid_id' => 1,
            'lot_data_lotacao' => now(),
            'lot_data_remocao' => null,
            'lot_portaria' => 'Portaria 125/2024',
        ]);
    }
}
