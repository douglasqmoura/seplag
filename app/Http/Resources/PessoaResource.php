<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PessoaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pessoa_id' => $this->pes_id,
            'nome' => $this->pes_nome,
            'data_nascimento' => $this->pes_data_nascimento,
            'sexo' => $this->pes_sexo,
            'mae' => $this->pes_mae,
            'pai' => $this->pes_pai,
            'endereco' => new EnderecoResource($this->whenLoaded('endereco')->first()),
            'fotos' => $this->whenLoaded('fotos', function () {
                return FotoPessoaResource::collection($this->fotos);
            }),
        ];
    }
}
