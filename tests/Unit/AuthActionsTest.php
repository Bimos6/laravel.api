<?php

namespace Tests\Unit;

use App\Actions\Auth\UserRegistrationAction;
use App\Actions\Auth\UserLoginAction;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthActionsTest extends TestCase
{
    public function test_user_registration_action_creates_user()
    {
        $action = new UserRegistrationAction();
        $user = $action->execute([
            'name' => 'Test User ' . uniqid(),
            'email' => 'test_' . uniqid() . '@example.com',
            'password' => 'password'
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    public function test_user_login_action_returns_user_with_valid_credentials()
    {
        $uniqueEmail = 'test_' . uniqid() . '@example.com';
        
        User::factory()->create([
            'email' => $uniqueEmail,
            'password' => bcrypt('password')
        ]);

        $action = new UserLoginAction();
        $user = $action->execute([
            'email' => $uniqueEmail,
            'password' => 'password'
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    public function test_user_login_action_fails_with_invalid_credentials()
    {
        $this->expectException(ValidationException::class);

        $action = new UserLoginAction();
        $action->execute([
            'email' => 'wrong_' . uniqid() . '@example.com',
            'password' => 'wrong'
        ]);
    }
}