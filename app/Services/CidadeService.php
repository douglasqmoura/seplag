<?php

namespace App\Services;

use App\Models\Cidade;

class CidadeService
{
    public function store(array $data): Cidade
    {
        $cidade = Cidade::firstOrCreate([
            'cid_nome' => $data['cidade'],
            'cid_uf' => strtoupper($data['uf']),
        ]);

        return $cidade;
    }
}
