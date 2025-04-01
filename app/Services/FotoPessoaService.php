<?php

namespace App\Services;

use App\Models\FotoPessoa;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FotoPessoaService
{
    public function store($foto, Pessoa $pessoa): FotoPessoa
    {
        $nomeArquivo = Str::uuid().'.'.$foto->getClientOriginalExtension();
        Storage::disk('s3')->put($nomeArquivo, file_get_contents($foto));

        return FotoPessoa::create([
            'pes_id' => $pessoa->pes_id,
            'fp_bucket' => config('filesystems.disks.s3.bucket'),
            'fp_hash' => $nomeArquivo,
            'fp_data' => now()->toDateString(),
        ]);
    }
}
