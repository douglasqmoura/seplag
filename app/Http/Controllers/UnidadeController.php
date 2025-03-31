<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\UnidadeRequest;
use App\Http\Resources\UnidadeResource;
use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\Unidade;
use App\Traits\VerificaPermissao;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{
    use VerificaPermissao;

    public function show($id)
    {
        try {
            $unidade = Unidade::with('endereco.cidade')->findOrFail($id);

            return new UnidadeResource($unidade);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unidade não encontrada',
            ], 404);
        }
    }

    public function index()
    {
        return PaginationHelper::paginate(
            Unidade::with('endereco.cidade'),
            UnidadeResource::class
        );
    }

    public function store(UnidadeRequest $request)
    {
        $this->verificarPermissao('unidade:store');

        return DB::transaction(function () use ($request) {
            // 1. Cria ou busca a cidade
            $cidade = Cidade::firstOrCreate([
                'cid_nome' => $request->cid_nome,
                'cid_uf' => strtoupper($request->cid_uf),
            ]);

            // 2. Cria o endereço com cid_id
            $endereco = Endereco::create([
                'end_tipo_logradouro' => $request->end_tipo_logradouro,
                'end_logradouro' => $request->end_logradouro,
                'end_numero' => $request->end_numero,
                'end_bairro' => $request->end_bairro,
                'cid_id' => $cidade->cid_id,
            ]);

            // 3. Cria a unidade
            $unidade = Unidade::create($request->only(['unid_nome', 'unid_sigla']));

            // 4. Relaciona endereço com unidade (via tabela pivô)
            $unidade->endereco()->sync([$endereco->end_id]);

            return new UnidadeResource($unidade->load('endereco.cidade'));
        });
    }

    public function update(UnidadeRequest $request, $id)
    {
        $this->verificarPermissao('unidade:update');

        return DB::transaction(function () use ($request, $id) {
            $unidade = Unidade::with('endereco')->findOrFail($id);
            $unidade->update($request->only(['unid_nome', 'unid_sigla']));

            // Cidade (atual ou nova)
            $cidade = Cidade::firstOrCreate([
                'cid_nome' => $request->cid_nome,
                'cid_uf' => strtoupper($request->cid_uf),
            ]);

            $dadosEndereco = [
                'end_tipo_logradouro' => $request->end_tipo_logradouro,
                'end_logradouro' => $request->end_logradouro,
                'end_numero' => $request->end_numero,
                'end_bairro' => $request->end_bairro,
                'cid_id' => $cidade->cid_id,
            ];

            $endereco = $unidade->endereco()->first();

            if ($endereco) {
                $endereco->update($dadosEndereco);
            } else {
                $endereco = Endereco::create($dadosEndereco);
                $unidade->endereco()->attach($endereco->end_id);
            }

            return new UnidadeResource($unidade->load('endereco.cidade'));
        });
    }

    public function destroy($id)
    {
        $this->verificarPermissao('unidade:destroy');

        try {
            return DB::transaction(function () use ($id) {
                $unidade = Unidade::findOrFail($id);

                $endereco = $unidade->endereco()->first();
                $unidade->endereco()->detach();
                $unidade->delete();
                if ($endereco) {
                    $endereco->delete();
                }

                return response()->json([
                    'message' => 'Unidade excluída com sucesso.',
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unidade não encontrada',
            ], 404);
        }
    }
}
