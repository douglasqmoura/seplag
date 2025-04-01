<?php

namespace App\Services;

use App\Models\Endereco;
use App\Models\Pessoa;

class PessoaService
{
    public function store(array $data, Endereco $endereco): Pessoa
    {
        $pessoa = Pessoa::create([
            'pes_nome' => $data['nome'],
            'pes_data_nascimento' => $data['data_nascimento'],
            'pes_sexo' => $data['sexo'],
            'pes_mae' => $data['mae'],
            'pes_pai' => $data['pai'],
        ]);

        $pessoa->endereco()->attach($endereco->end_id);

        return $pessoa;
    }

    public function update(array $data, Pessoa $pessoa): Pessoa
    {
        $pessoa->update([
            'pes_nome' => $data['nome'],
            'pes_data_nascimento' => $data['data_nascimento'],
            'pes_sexo' => $data['sexo'],
            'pes_mae' => $data['mae'],
            'pes_pai' => $data['pai'],
        ]);

        return $pessoa;
    }
}
