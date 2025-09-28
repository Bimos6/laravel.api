<?php

namespace Tests\Unit;

use App\Actions\Auth\UserRegistrationAction;
use App\Actions\Auth\UserLoginAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthActionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_registration_action_creates_user()
    {
        $action = new UserRegistrationAction();
        $user = $action->execute([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->email);
    }

    /** @test */
    public function user_login_action_returns_user_with_valid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $action = new UserLoginAction();
        $user = $action->execute([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function user_login_action_fails_with_invalid_credentials()
    {
        $this->expectException(ValidationException::class);

        $action = new UserLoginAction();
        $action->execute([
            'email' => 'wrong@example.com',
            'password' => 'wrong'
        ]);
    }
}