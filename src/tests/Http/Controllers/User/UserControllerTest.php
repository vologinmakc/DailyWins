<?php

namespace Tests\Http\Controllers\User;


use App\Constants\Response\ResponseStatuses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        $this->user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
        $this->accessToken = $this->user->createToken('test-token')->accessToken;

    }

    public function withHeaders(array $headers)
    {
        $headers['Authorization'] = 'Bearer ' . $this->accessToken;
        return parent::withHeaders($headers);
    }

    public function testItReturnsAuthenticatedUser()
    {
        // Делаем запрос к методу me
        $response = $this->withHeaders([])->getJson('/api/user/me');

        // Проверяем, что ответ успешный и содержит данные текущего пользователя
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->user->id)
            ->assertJsonPath('data.name', 'admin')
            ->assertJsonPath('data.email', 'admin@admin.com');
    }

    public function testItReturnsErrorForUnauthenticatedUser()
    {
        // Делаем запрос к методу me
        $response = $this->getJson('/api/user/me');

        $response->assertStatus(500);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);
    }
}
