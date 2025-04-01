<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServidorEfetivoPorUnidadeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pessoa = $this->pessoa;
        $foto = $pessoa->fotos->last();

        return [
            'nome' => $pessoa->pes_nome,
            'idade' => Carbon::parse($pessoa->pes_data_nascimento)->age,
            'unidade' => $this->unidade->unid_nome,
            'foto' => new FotoPessoaResource($foto),
        ];
    }
}
