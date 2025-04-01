<?php

namespace App\Http\Controllers;

use App\Http\Resources\FotoPessoaResource;
use App\Models\Lotacao;
use Carbon\Carbon;

class ConsultaServidorEfetivoController extends Controller
{
    public function porUnidade($unid_id)
    {
        try {
            $lotacoes = Lotacao::with(['pessoa.fotos', 'unidade'])
                ->where('unid_id', $unid_id)
                ->whereNull('lot_data_remocao')
                ->get();

            $resultado = $lotacoes->map(function ($lotacao) {
                $pessoa = $lotacao->pessoa;
                $foto = $pessoa->fotos->last();

                return [
                    'nome' => $pessoa->pes_nome,
                    'idade' => Carbon::parse($pessoa->pes_data_nascimento)->age,
                    'unidade' => $lotacao->unidade->unid_nome,
                    'foto' => new FotoPessoaResource($foto),
                ];
            });

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao consultar servidores efetivos por unidade.'], 500);
        }
    }
}
