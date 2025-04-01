<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\ServidorEfetivoEnderecoFuncionalResource;
use App\Http\Resources\ServidorEfetivoPorUnidadeResource;
use App\Models\Lotacao;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class ConsultaServidorEfetivoController extends Controller
{
    public function porUnidade($unid_id)
    {
        try {
            $lotacoes = Lotacao::with(['pessoa.fotos', 'unidade'])
                ->where('unid_id', $unid_id)
                ->whereNull('lot_data_remocao');

            return PaginationHelper::paginate($lotacoes, ServidorEfetivoPorUnidadeResource::class);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao consultar servidores efetivos por unidade.', 'erro' => $e->getMessage()], 500);
        }
    }

    public function enderecoFuncionalPorNome(Request $request)
    {
        $nomeParcial = $request->input('nome');

        try {
            $pessoas = Pessoa::where('pes_nome', 'ILIKE', "%{$nomeParcial}%")
                ->with(['lotacoes' => function ($query) {
                    $query->whereNull('lot_data_remocao')
                        ->with(['unidade.endereco.cidade']);
                }])
                ->get();

            $coletados = collect();

            foreach ($pessoas as $pessoa) {
                foreach ($pessoa->lotacoes as $lotacao) {
                    $coletados->push([
                        'pessoa' => $pessoa,
                        'lotacao' => $lotacao,
                    ]);
                }
            }

            return PaginationHelper::paginate($coletados, ServidorEfetivoEnderecoFuncionalResource::class);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao consultar endereço funcional.', 'erro' => $e->getMessage()], 500);
        }
    }
}
