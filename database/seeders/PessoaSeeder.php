<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Illuminate\Database\Seeder;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pessoa::create([
            'pes_nome' => 'João da Silva',
            'pes_data_nascimento' => '1990-01-01',
            'pes_sexo' => 'Masculino',
            'pes_mae' => 'Maria Silva',
            'pes_pai' => 'José Silva',
        ]);
        Pessoa::create([
            'pes_nome' => 'Maria Aparecida Antunes',
            'pes_data_nascimento' => '1978-04-15',
            'pes_sexo' => 'Feminino',
            'pes_mae' => 'Tereza Antunes',
            'pes_pai' => 'Roberto Antunes',
        ]);
    }
}
