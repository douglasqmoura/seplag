<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnderecoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->end_id,
            'tipo_logradouro' => $this->end_tipo_logradouro,
            'logradouro' => $this->end_logradouro,
            'numero' => $this->end_numero,
            'bairro' => $this->end_bairro,
            'cidade' => $this->cidade->cid_nome,
            'uf' => $this->cidade->cid_uf,
        ];
    }
}
