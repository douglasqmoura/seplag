<?php

namespace Database\Seeders;

use App\Models\Cidade;
use Illuminate\Database\Seeder;

class CidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cidade::create(['cid_nome' => 'Alta Floresta', 'cid_uf' => 'MT']);
        Cidade::create(['cid_nome' => 'Cuiabá', 'cid_uf' => 'MT']);
        Cidade::create(['cid_nome' => 'Sinop', 'cid_uf' => 'MT']);
    }
}
