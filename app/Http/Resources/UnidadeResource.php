<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnidadeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $endereco = $this->endereco;

        return [
            'id' => $this->unid_id,
            'nome' => $this->unid_nome,
            'sigla' => $this->unid_sigla,

            'endereço' => $endereco
                ? "{$endereco->end_tipo_logradouro} {$endereco->end_logradouro}, {$endereco->end_numero}"
                : null,

            'bairro' => $endereco->end_bairro ?? null,
            'cidade' => $endereco->cidade->cid_nome ?? null,
            'uf' => $endereco->cidade->cid_uf ?? null,
        ];
    }
}
