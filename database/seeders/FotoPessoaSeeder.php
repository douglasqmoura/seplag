<?php

namespace Database\Seeders;

use App\Models\FotoPessoa;
use Illuminate\Database\Seeder;

class FotoPessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FotoPessoa::create([
            'pes_id' => 1,
            'fp_data' => now(),
            'fp_bucket' => 'default',
            'fp_hash' => 'abc123',
        ]);
    }
}
