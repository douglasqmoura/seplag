<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unidade::create([
            'unid_nome' => 'Secretaria de Administração',
            'unid_sigla' => 'SADM',
        ]);
    }
}
