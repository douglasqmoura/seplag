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
        return [
            'id' => $this->unid_id,
            'nome' => $this->unid_nome,
            'sigla' => $this->unid_sigla,
            'endereco' => new EnderecoResource($this->whenLoaded('endereco')->first()),
        ];
    }
}
