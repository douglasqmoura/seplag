<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $user->tokens()->delete();
            $abilities = $user->admin ? ['*'] : [];
            $token = $user->createToken('api-token', $abilities, Carbon::now()->addMinutes(config('sanctum.token_expiration')));

            return response()->json(['token' => $token->plainTextToken, 'expires_at' => $token->accessToken->expires_at->toDateTimeString()], 200);
        }

        return response()->json(['error' => 'Não Autorizado'], 401);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->expires_at = Carbon::now()->addMinutes(config('sanctum.token_expiration'));
            $token->save();

            return response()->json(['message' => 'Token renovado com sucesso até '.$token->expires_at->toDateTimeString()], 200);
        }

        return response()->json(['message' => 'Token não pode ser renovado.'], 400);
    }
}
