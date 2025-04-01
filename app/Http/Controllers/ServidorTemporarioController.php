<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\ServidorTemporarioRequest;
use App\Http\Resources\ServidorTemporarioResource;
use App\Models\FotoPessoa;
use App\Models\ServidorTemporario;
use App\Services\CidadeService;
use App\Services\EnderecoService;
use App\Services\FotoPessoaService;
use App\Services\PessoaService;
use App\Traits\VerificaPermissao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServidorTemporarioController extends Controller
{
    use VerificaPermissao;

    public function __construct(
        protected CidadeService $cidadeService,
        protected EnderecoService $enderecoService,
        protected PessoaService $pessoaService,
        protected FotoPessoaService $fotoPessoaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PaginationHelper::paginate(
            ServidorTemporario::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
            ]),
            ServidorTemporarioResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServidorTemporarioRequest $request)
    {
        $this->verificarPermissao('servidor-temporario:store');

        try {
            return DB::transaction(function () use ($request) {

                $cidade = $this->cidadeService->store($request->toArray());
                $endereco = $this->enderecoService->store($request->toArray(), $cidade);
                $pessoa = $this->pessoaService->store($request->toArray(), $endereco);

                $servidor = ServidorTemporario::create([
                    'pes_id' => $pessoa->pes_id,
                    'st_data_admissao' => $request->input('data_admissao'),
                    'st_data_demissao' => $request->input('data_demissao'),
                ]);

                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $this->fotoPessoaService->store($foto, $pessoa);
                    }
                }

                return new ServidorTemporarioResource($servidor->load(['pessoa.endereco.cidade', 'pessoa.fotos']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível criar o servidor temporário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $servidor = ServidorTemporario::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
            ])->findOrFail($id);

            return new ServidorTemporarioResource($servidor);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor temporário não encontrado.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServidorTemporarioRequest $request, string $id)
    {

        $this->verificarPermissao('servidor-temporario:update');

        try {

            return DB::transaction(function () use ($request, $id) {

                $servidor = ServidorTemporario::with('pessoa.endereco.cidade')->findOrFail($id);

                $cidade = $this->cidadeService->store($request->toArray());
                $endereco = $this->enderecoService->update($request->toArray(), $servidor->pessoa->endereco->first(), $cidade);
                $servidor->pessoa->endereco()->syncWithoutDetaching($endereco->end_id);
                $pessoa = $this->pessoaService->update($request->toArray(), $servidor->pessoa);

                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $this->fotoPessoaService->store($foto, $pessoa);
                    }
                }

                $servidor->update([
                    'pes_id' => $pessoa->pes_id,
                    'st_data_admissao' => $request->input('data_admissao'),
                    'st_data_demissao' => $request->input('data_demissao'),
                ]);

                // Se a requisição incluir fotos para excluir
                if ($request->has('remover_fotos')) {
                    $idsParaRemover = $request->input('remover_fotos');

                    $fotosParaRemover = FotoPessoa::whereIn('fp_id', $idsParaRemover)
                        ->where('pes_id', $servidor->pessoa->pes_id)
                        ->get();

                    foreach ($fotosParaRemover as $foto) {
                        $files = Storage::disk('s3')->files('');
                        foreach ($files as $file) {
                            if (Str::startsWith(basename($file), $foto->fp_hash)) {
                                Storage::disk('s3')->delete($file);
                            }
                        }
                        $foto->delete();
                    }
                }

                return new ServidorTemporarioResource($servidor->fresh(['pessoa.endereco.cidade', 'pessoa.fotos']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor temporário não encontrado.'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->verificarPermissao('servidor-temporario:destroy');

        try {
            return DB::transaction(function () use ($id) {
                $servidor = ServidorTemporario::with('pessoa.fotos')->findOrFail($id);

                $pessoa = $servidor->pessoa;

                // Verifica e remove as fotos da pessoa
                if ($pessoa->fotos && $pessoa->fotos->count() > 0) {
                    foreach ($pessoa->fotos as $foto) {
                        // Remove o arquivo físico do bucket
                        $files = Storage::disk('s3')->files('');
                        foreach ($files as $file) {
                            if (Str::startsWith(basename($file), $foto->fp_hash)) {
                                Storage::disk('s3')->delete($file);
                            }
                        }
                        $foto->delete();
                    }
                }

                $servidor->pessoa->endereco?->first()?->delete();
                $servidor->pessoa->delete();
                $servidor->delete();

                return response()->json(['message' => 'Servidor temporário excluído com sucesso.']);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor temporário não encontrado.', 'error' => $e->getMessage()], 404);
        }
    }
}
