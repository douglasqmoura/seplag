<?php

namespace App\Traits;

trait VerificaPermissao
{
    public function verificarPermissao(string $permissao): void
    {
        if (! auth()->user()?->tokenCan($permissao)) {
            abort(response()->json(['message' => 'Ação não autorizada.'], 403));
        }
    }
}
