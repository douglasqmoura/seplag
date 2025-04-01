<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\ServidorEfetivoRequest;
use App\Http\Resources\ServidorEfetivoResource;
use App\Models\FotoPessoa;
use App\Models\ServidorEfetivo;
use App\Services\CidadeService;
use App\Services\EnderecoService;
use App\Services\FotoPessoaService;
use App\Services\PessoaService;
use App\Traits\VerificaPermissao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServidorEfetivoController extends Controller
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
            ServidorEfetivo::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
            ]),
            ServidorEfetivoResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServidorEfetivoRequest $request)
    {
        $this->verificarPermissao('servidor-efetivo:store');

        try {
            return DB::transaction(function () use ($request) {

                $cidade = $this->cidadeService->store($request->toArray());
                $endereco = $this->enderecoService->store($request->toArray(), $cidade);
                $pessoa = $this->pessoaService->store($request->toArray(), $endereco);

                $servidor = ServidorEfetivo::create([
                    'se_matricula' => $request->matricula,
                    'pes_id' => $pessoa->pes_id,
                ]);

                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $this->fotoPessoaService->store($foto, $pessoa);
                    }
                }

                return new ServidorEfetivoResource($servidor->load(['pessoa.endereco.cidade', 'pessoa.fotos']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Não foi possível criar o servidor efetivo.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $matricula)
    {
        try {
            $servidor = ServidorEfetivo::with([
                'pessoa.endereco.cidade',
                'pessoa.fotos',
            ])->where('se_matricula', $matricula)->firstOrFail();

            return new ServidorEfetivoResource($servidor);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor efetivo não encontrado.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServidorEfetivoRequest $request, string $matricula)
    {
        $this->verificarPermissao('servidor-efetivo:update');

        try {

            return DB::transaction(function () use ($request, $matricula) {

                $servidor = ServidorEfetivo::with('pessoa.endereco.cidade')->where('se_matricula', $matricula)->firstOrFail();

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
                    'se_matricula' => $request->matricula,
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

                return new ServidorEfetivoResource($servidor->fresh(['pessoa.endereco.cidade', 'pessoa.fotos']));
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor efetivo não encontrado.'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $matricula)
    {
        $this->verificarPermissao('servidor-efetivo:destroy');

        try {
            return DB::transaction(function () use ($matricula) {
                $servidor = ServidorEfetivo::with('pessoa.fotos')->where('se_matricula', $matricula)->firstOrFail();
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

                return response()->json(['message' => 'Servidor efetivo excluído com sucesso.']);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor efetivo não encontrado.', 'error' => $e->getMessage()], 404);
        }
    }
}
