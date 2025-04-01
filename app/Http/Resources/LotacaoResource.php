<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LotacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->lot_id,
            'data_lotacao' => $this->lot_data_lotacao,
            'data_remocao' => $this->lot_data_remocao,
            'portaria' => $this->lot_portaria,
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
            'unidade' => new UnidadeResource($this->whenLoaded('unidade')),
        ];
    }
}
