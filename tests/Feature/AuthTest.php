<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_and_login()
    {
        // Регистрация
        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ])->assertStatus(201)
          ->assertJsonStructure(['user', 'access_token']);

        // Логин
        $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ])->assertStatus(200)
          ->assertJsonStructure(['user', 'access_token']);
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $this->postJson('/api/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function login_fails_with_wrong_credentials()
    {
        $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong'
        ])->assertStatus(422);
    }
}