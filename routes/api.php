<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refreshToken'])
            ->name('refresh');
    });
});

Route::get('/user', function (Request $request) {
    abort_if(! auth()->user()->tokenCan('gravar'), 403, 'Unauthorized');

    return $request->user();
})->middleware('auth:sanctum');
