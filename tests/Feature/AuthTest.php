<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register_and_login()
    {
        $uniqueEmail = 'test_' . uniqid() . '@example.com';

        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => $uniqueEmail,
            'password' => 'password'
        ])->assertStatus(201)
          ->assertJsonStructure(['user', 'access_token']);

        $this->postJson('/api/auth/login', [
            'email' => $uniqueEmail,
            'password' => 'password'
        ])->assertStatus(200)
          ->assertJsonStructure(['user', 'access_token']);
    }

    public function test_registration_fails_with_invalid_data()
    {
        $this->postJson('/api/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_fails_with_wrong_credentials()
    {
        $this->postJson('/api/auth/login', [
            'email' => 'wrong_' . uniqid() . '@example.com',
            'password' => 'wrong'
        ])->assertStatus(422);
    }
}