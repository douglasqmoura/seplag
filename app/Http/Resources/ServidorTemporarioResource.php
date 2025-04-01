<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServidorTemporarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data_admissao' => $this->st_data_admissao,
            'data_demissao' => $this->st_data_demissao,
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
        ];
    }
}
