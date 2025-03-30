<?php

namespace Database\Seeders;

use App\Models\Endereco;
use Illuminate\Database\Seeder;

class EnderecoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Endereco::create([
            'end_tipo_logradouro' => 'Rua',
            'end_logradouro' => 'Av. Brasil',
            'end_numero' => 100,
            'end_bairro' => 'Centro',
            'cid_id' => 1,
        ]);
    }
}
