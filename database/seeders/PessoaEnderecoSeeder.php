<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PessoaEnderecoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pessoa_endereco')->insert([
            'pes_id' => 1,
            'end_id' => 1,
        ]);
    }
}
