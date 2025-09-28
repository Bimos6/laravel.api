<?php

namespace App\Actions\Auth;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class CreateTokenAction
{
    public function execute(User $user, string $name = 'auth-token'): NewAccessToken
    {
        return $user->createToken($name);
    }
}