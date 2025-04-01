<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServidorEfetivoEnderecoFuncionalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pessoa = $this['pessoa'];
        $lotacao = $this['lotacao'];
        $endereco = $lotacao->unidade->endereco->first();
        $cidade = $endereco?->cidade;

        return [
            'nome' => $pessoa->pes_nome,
            'unidade' => $lotacao->unidade->unid_nome,
            'endereco_funcional' => $endereco ? [
                'logradouro' => $endereco->end_logradouro,
                'numero' => $endereco->end_numero,
                'bairro' => $endereco->end_bairro,
                'cidade' => $cidade?->cid_nome,
                'uf' => $cidade?->cid_uf,
            ] : null,
        ];
    }
}
