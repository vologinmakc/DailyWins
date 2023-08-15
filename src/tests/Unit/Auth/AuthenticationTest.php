<?php

namespace Tests\Unit\Auth;

use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testUserCanAuthenticateWithValidCredentials()
    {
        $this->seed(UsersTableSeeder::class);

        $user = \App\Models\User::first();

        $client = \Laravel\Passport\Client::factory()->create([
            'password_client' => true
        ]);

        config(['auth.passport.client_id' => $client->id]);
        config(['auth.passport.client_secret' => $client->secret]);

        $response = $this->post('/api/login', [
            'username' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());
    }
}
