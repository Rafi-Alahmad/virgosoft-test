<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_returns_balance_and_assets(): void
    {
        $user = User::factory()->create([
            'balance' => 1250.50,
        ]);

        Asset::factory()->for($user)->create([
            'symbol' => 'BTC',
            'amount' => 0.5,
            'locked_amount' => 0.1,
        ]);

        Asset::factory()->for($user)->create([
            'symbol' => 'ETH',
            'amount' => 2.25,
            'locked_amount' => 0,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'balance' => '1250.50',
                    'assets' => [
                        ['symbol' => 'BTC', 'amount' => '0.50000000', 'locked_amount' => '0.10000000'],
                        ['symbol' => 'ETH', 'amount' => '2.25000000', 'locked_amount' => '0.00000000'],
                    ],
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'balance',
                    'assets' => [
                        '*' => ['symbol', 'amount', 'locked_amount'],
                    ],
                ],
            ]);
    }
}

