<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaServidorEfetivoController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('refresh-token', [AuthController::class, 'refreshToken'])
        ->name('refresh');

    Route::apiResource('unidade', UnidadeController::class);

    Route::apiResource('servidor-efetivo', ServidorEfetivoController::class);

    Route::apiResource('servidor-temporario', ServidorTemporarioController::class);

    Route::apiResource('lotacao', LotacaoController::class);

    Route::get('consultas/servidores-efetivos/por-unidade/{unid_id}', [ConsultaServidorEfetivoController::class, 'porUnidade']);
    Route::get('consultas/servidores-efetivos/endereco-funcional', [ConsultaServidorEfetivoController::class, 'enderecoFuncionalPorNome']);
});
