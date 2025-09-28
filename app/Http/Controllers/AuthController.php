<?php

namespace App\Http\Controllers;

use App\Actions\Auth\UserRegistrationAction;
use App\Actions\Auth\UserLoginAction;
use App\Actions\Auth\CreateTokenAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        UserRegistrationAction $registerAction,
        CreateTokenAction $createTokenAction
    ): JsonResponse {
        $user = $registerAction->execute($request->validated());
        $token = $createTokenAction->execute($user);

        return response()->json([
            'user' => $user,
            'access_token' => $token->plainTextToken,
        ], 201);
    }

    public function login(
        LoginRequest $request,
        UserLoginAction $loginAction,
        CreateTokenAction $createTokenAction
    ): JsonResponse {
        $user = $loginAction->execute($request->validated());
        $token = $createTokenAction->execute($user);

        return response()->json([
            'user' => $user,
            'access_token' => $token->plainTextToken,
        ]);
    }
}