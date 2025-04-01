<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('refresh-token', [AuthController::class, 'refreshToken'])
        ->name('refresh');

    Route::apiResource('unidades', UnidadeController::class);

    Route::apiResource('servidor-efetivo', ServidorEfetivoController::class);

    Route::apiResource('servidor-temporario', ServidorTemporarioController::class);

    Route::get('/user', function (Request $request) {
        abort_if(! auth()->user()->tokenCan('gravar'), 403, 'Unauthorized');

        return $request->user();
    });
});
