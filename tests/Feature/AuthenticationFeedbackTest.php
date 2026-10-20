<?php

namespace Tests\Feature;

use App\Models\User;
use App\Http\Controllers\Frontend\LoginController;
use App\Http\Controllers\Professional\ProfessionalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class AuthenticationFeedbackTest extends TestCase
{
    public function test_customer_password_mutator_hashes_password_once_with_configured_hasher(): void
    {
        $password = 'customer-password-for-test';
        $user = new User();
        $user->forceFill(['password' => $password]);

        $storedHash = $user->getAttributes()['password'];

        $this->assertNotSame($password, $storedHash);
        $this->assertTrue(Hash::check($password, $storedHash));
    }

    public function test_bcrypt_verification_rejects_a_legacy_argon_hash(): void
    {
        config([
            'hashing.driver' => 'bcrypt',
            'hashing.bcrypt.verify' => true,
        ]);

        $legacyHash = Hash::driver('argon')->make('legacy-password-for-test');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This password does not use the Bcrypt algorithm.');

        Hash::check('legacy-password-for-test', $legacyHash);
    }

    public function test_customer_login_returns_a_generic_response_for_unexpected_hash_errors(): void
    {
        $guard = Mockery::mock();
        $guard->shouldReceive('validate')
            ->once()
            ->andThrow(new RuntimeException('Sensitive internal details'));

        Auth::shouldReceive('guard')->once()->with('user')->andReturn($guard);
        Log::shouldReceive('error')
            ->once()
            ->with('Customer login failed unexpectedly.', Mockery::on(function (array $context): bool {
                return $context['exception_class'] === RuntimeException::class
                    && $context['exception_code'] === 0
                    && is_string($context['exception_file'])
                    && is_int($context['exception_line']);
            }));

        $response = app(LoginController::class)->login($this->makeLoginRequest([
            'email' => 'customer@example.test',
            'password' => 'valid-test-password',
        ]));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
                'status' => 'error',
                'message' => "We couldn't complete your request right now. Please try again later.",
            ], $response->getData(true));
        $this->assertStringNotContainsString('Sensitive internal details', $response->getContent());
    }

    public function test_professional_login_returns_a_generic_response_for_unexpected_hash_errors(): void
    {
        $guard = Mockery::mock();
        $guard->shouldReceive('attempt')
            ->once()
            ->andThrow(new RuntimeException('Sensitive internal details'));

        Auth::shouldReceive('guard')->once()->with('professional')->andReturn($guard);
        Log::shouldReceive('error')
            ->once()
            ->with('Professional login failed unexpectedly.', Mockery::on(function (array $context): bool {
                return $context['exception_class'] === RuntimeException::class
                    && $context['exception_code'] === 0
                    && is_string($context['exception_file'])
                    && is_int($context['exception_line']);
            }));

        $response = app(ProfessionalController::class)->store($this->makeLoginRequest([
            'email' => 'professional@example.test',
            'password' => 'valid-test-password',
        ]));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
                'status' => 'error',
                'message' => "We couldn't complete your request right now. Please try again later.",
            ], $response->getData(true));
        $this->assertStringNotContainsString('Sensitive internal details', $response->getContent());
    }

    public function test_customer_and_professional_invalid_credentials_use_the_same_generic_message(): void
    {
        $customerGuard = Mockery::mock();
        $customerGuard->shouldReceive('validate')->twice()->andReturnFalse();
        Auth::shouldReceive('guard')->twice()->with('user')->andReturn($customerGuard);

        $customerResponses = [
            app(LoginController::class)->login($this->makeLoginRequest([
                'email' => 'unknown@example.test',
                'password' => 'wrong-test-password',
            ])),
            app(LoginController::class)->login($this->makeLoginRequest([
                'email' => 'customer@example.test',
                'password' => 'wrong-test-password',
            ])),
        ];

        $professionalGuard = Mockery::mock();
        $professionalGuard->shouldReceive('attempt')->twice()->andReturnFalse();
        Auth::shouldReceive('guard')->twice()->with('professional')->andReturn($professionalGuard);

        $professionalResponses = [
            app(ProfessionalController::class)->store($this->makeLoginRequest([
                'email' => 'unknown@example.test',
                'password' => 'wrong-test-password',
            ])),
            app(ProfessionalController::class)->store($this->makeLoginRequest([
                'email' => 'professional@example.test',
                'password' => 'wrong-test-password',
            ])),
        ];

        $expected = [
            'status' => 'error',
            'message' => 'Incorrect email or password. Please check your details and try again.',
        ];

        foreach (array_merge($customerResponses, $professionalResponses) as $response) {
            $this->assertSame(401, $response->getStatusCode());
            $this->assertSame($expected, $response->getData(true));
        }
    }

    public function test_customer_professional_and_force_login_routes_are_throttled(): void
    {
        foreach ([
            ['/login', 'POST'],
            ['/professional/store', 'POST'],
            ['/force-login', 'POST'],
        ] as [$uri, $method]) {
            $route = app('router')->getRoutes()->match(Request::create($uri, $method));

            $this->assertContains('throttle:5,1', $route->gatherMiddleware(), $uri);
        }
    }

    private function makeLoginRequest(array $input): Request
    {
        return Request::create('/login', 'POST', $input);
    }
}
