<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\ServidorEfetivoPorUnidadeResource;
use App\Models\Lotacao;

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
}
