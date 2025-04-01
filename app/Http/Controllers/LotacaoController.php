<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\LotacaoRequest;
use App\Http\Resources\LotacaoResource;
use App\Models\Lotacao;
use App\Traits\VerificaPermissao;
use Illuminate\Support\Facades\DB;

class LotacaoController extends Controller
{
    use VerificaPermissao;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PaginationHelper::paginate(
            Lotacao::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
                'unidade.endereco.cidade',
            ]),
            LotacaoResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LotacaoRequest $request)
    {
        $this->verificarPermissao('lotacao:store');

        try {
            return DB::transaction(function () use ($request) {
                $lotacao = Lotacao::create($request->only([
                    'pes_id',
                    'unid_id',
                    'lot_data_lotacao',
                    'lot_data_remocao',
                    'lot_portaria',
                ]));

                return new LotacaoResource($lotacao->load(['pessoa.endereco', 'unidade.endereco']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar lotação.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $lotacao = Lotacao::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
                'unidade.endereco.cidade',
            ])->findOrFail($id);

            return new LotacaoResource($lotacao);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lotação não encontrada.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LotacaoRequest $request, string $id)
    {
        $this->verificarPermissao('lotacao:update');

        try {
            return DB::transaction(function () use ($request, $id) {
                $lotacao = Lotacao::findOrFail($id);

                $lotacao->update($request->only([
                    'pes_id',
                    'unid_id',
                    'lot_data_lotacao',
                    'lot_data_remocao',
                    'lot_portaria',
                ]));

                return new LotacaoResource($lotacao->fresh(['pessoa.endereco', 'unidade.endereco']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar lotação.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->verificarPermissao('lotacao:destroy');

        try {
            Lotacao::findOrFail($id)->delete();

            return response()->json(['message' => 'Lotação excluída com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lotação não encontrada.'], 404);
        }
    }
}
