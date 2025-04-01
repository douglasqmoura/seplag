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
        Endereco::create([
            'end_tipo_logradouro' => 'Rua',
            'end_logradouro' => 'Av. das Araras',
            'end_numero' => 400,
            'end_bairro' => 'Jardim das Araras',
            'cid_id' => 2,
        ]);
        Endereco::create([
            'end_tipo_logradouro' => 'Avenida',
            'end_logradouro' => 'Av. das Avencas',
            'end_numero' => 400,
            'end_bairro' => 'Centro Norte',
            'cid_id' => 3,
        ]);
    }
}
