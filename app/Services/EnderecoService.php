<?php

namespace App\Services;

use App\Models\Cidade;
use App\Models\Endereco;

class EnderecoService
{
    public function store(array $data, Cidade $cidade): Endereco
    {
        $endereco = Endereco::create([
            'end_tipo_logradouro' => $data['tipo_logradouro'],
            'end_logradouro' => $data['logradouro'],
            'end_numero' => $data['numero'],
            'end_bairro' => $data['bairro'],
            'cid_id' => $cidade->cid_id,
        ]);

        return $endereco;
    }

    public function update(array $data, ?Endereco $endereco, Cidade $cidade): Endereco
    {
        $dadosEndereco = [
            'end_tipo_logradouro' => $data['tipo_logradouro'],
            'end_logradouro' => $data['logradouro'],
            'end_numero' => $data['numero'],
            'end_bairro' => $data['bairro'],
            'cid_id' => $cidade->cid_id,
        ];

        if ($endereco) {
            $endereco->update($dadosEndereco);
        } else {
            $endereco = Endereco::create($dadosEndereco);
        }

        return $endereco;
    }
}
