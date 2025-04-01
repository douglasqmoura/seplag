<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\ServidorEfetivoRequest;
use App\Http\Resources\ServidorEfetivoResource;
use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\FotoPessoa;
use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use App\Traits\VerificaPermissao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServidorEfetivoController extends Controller
{
    use VerificaPermissao;

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

                // Cria cidade
                $cidade = Cidade::firstOrCreate([
                    'cid_nome' => $request->input('cidade'),
                    'cid_uf' => strtoupper($request->input('uf')),
                ]);

                // Cria endereço
                $endereco = Endereco::create([
                    'end_tipo_logradouro' => $request->input('tipo_logradouro'),
                    'end_logradouro' => $request->input('logradouro'),
                    'end_numero' => $request->input('numero'),
                    'end_bairro' => $request->input('bairro'),
                    'cid_id' => $cidade->cid_id,
                ]);

                // Cria pessoa
                $pessoa = Pessoa::create([
                    'pes_nome' => $request->input('nome'),
                    'pes_data_nascimento' => $request->input('data_nascimento'),
                    'pes_sexo' => $request->input('sexo'),
                    'pes_mae' => $request->input('mae'),
                    'pes_pai' => $request->input('pai'),
                ]);

                $pessoa->endereco()->attach($endereco->end_id);

                // Cria servidor
                $servidor = ServidorEfetivo::create([
                    'se_matricula' => $request->matricula,
                    'pes_id' => $pessoa->pes_id,
                ]);

                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $hash = Str::uuid()->toString();

                        $path = "{$hash}." . $foto->getClientOriginalExtension();

                        Storage::disk('s3')->put($path, file_get_contents($foto));
                        FotoPessoa::create([
                            'pes_id' => $pessoa->pes_id,
                            'fp_bucket' => config('filesystems.disks.s3.bucket'),
                            'fp_hash' => $path,
                            'fp_data' => now()->toDateString(),
                        ]);
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

                $cidade = Cidade::firstOrCreate([
                    'cid_nome' => $request->cidade,
                    'cid_uf' => strtoupper($request->uf),
                ]);

                $endereco = $servidor->pessoa->endereco->first();
                $dadosEndereco = [
                    'end_tipo_logradouro' => $request->tipo_logradouro,
                    'end_logradouro' => $request->logradouro,
                    'end_numero' => $request->numero,
                    'end_bairro' => $request->bairro,
                    'cid_id' => $cidade->cid_id,
                ];

                if ($endereco) {
                    $endereco->update($dadosEndereco);
                } else {
                    $endereco = Endereco::create($dadosEndereco);
                    $servidor->pessoa->endereco()->attach($endereco->end_id);
                }

                $servidor->pessoa->update([
                    'pes_nome' => $request->nome,
                    'pes_data_nascimento' => $request->data_nascimento,
                    'pes_sexo' => $request->sexo,
                    'pes_mae' => $request->mae,
                    'pes_pai' => $request->pai,
                ]);

                $servidor->update([
                    'se_matricula' => $request->matricula,
                ]);

                // Processa novas fotos se enviadas
                if ($request->hasFile('fotos')) {
                    foreach ($request->file('fotos') as $foto) {
                        $hash = Str::uuid()->toString();

                        $path = "{$hash}." . $foto->getClientOriginalExtension();

                        Storage::disk('s3')->put($path, file_get_contents($foto));
                        FotoPessoa::create([
                            'pes_id' => $servidor->pessoa->pes_id,
                            'fp_bucket' => config('filesystems.disks.s3.bucket'),
                            'fp_hash' => $path,
                            'fp_data' => now()->toDateString(),
                        ]);
                    }
                }

                // Se a requisição incluir fotos para excluir
                if ($request->has('remover_fotos')) {
                    $idsParaRemover = $request->input('remover_fotos');

                    // Busca as fotos pelo ID
                    $fotosParaRemover = FotoPessoa::whereIn('fp_id', $idsParaRemover)
                        ->where('pes_id', $servidor->pessoa->pes_id)
                        ->get();

                    foreach ($fotosParaRemover as $foto) {
                        // Remove o arquivo do storage
                        $path = "{$foto->fp_bucket}/{$foto->fp_hash}";

                        // Tenta encontrar e remover qualquer arquivo que comece com o hash
                        $files = Storage::disk('s3')->files($foto->fp_bucket);
                        foreach ($files as $file) {
                            if (Str::startsWith(basename($file), $foto->fp_hash)) {
                                Storage::disk('s3')->delete($file);
                            }
                        }

                        // Remove o registro do banco de dados
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
                        $files = Storage::disk('s3')->files($foto->fp_bucket);
                        foreach ($files as $file) {
                            if (Str::startsWith(basename($file), $foto->fp_hash)) {
                                Storage::disk('s3')->delete($file);
                            }
                        }
                        // Remove o registro da foto do banco de dados
                        $foto->delete();
                    }
                }

                $servidor->delete();
                $servidor->pessoa->endereco?->first()->delete();
                $servidor->pessoa->delete();

                return response()->json(['message' => 'Servidor efetivo excluído com sucesso.']);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Servidor efetivo não encontrado.', 'error' => $e->getMessage()], 404);
        }
    }
}
