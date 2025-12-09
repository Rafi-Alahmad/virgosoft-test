<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use App\Events\OrderMatchedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_buy_order_requires_sufficient_balance(): void
    {
        $user = User::factory()->create([
            'balance' => 50,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 100,
            'amount' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['balance']);
    }

    public function test_buy_order_deducts_balance_and_opens(): void
    {
        $user = User::factory()->create([
            'balance' => 500,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 100,
            'amount' => 2,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', Order::STATUS_OPEN);

        $user->refresh();
        $this->assertEquals('300.00000000', number_format($user->balance, 8, '.', ''));
    }

    public function test_sell_order_requires_sufficient_asset(): void
    {
        $user = User::factory()->create();
        Asset::factory()->for($user)->create([
            'symbol' => 'ETH',
            'amount' => 0.1,
            'locked_amount' => 0,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'symbol' => 'ETH',
            'side' => 'sell',
            'price' => 1000,
            'amount' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_sell_order_moves_to_locked_and_opens(): void
    {
        $user = User::factory()->create();
        Asset::factory()->for($user)->create([
            'symbol' => 'ETH',
            'amount' => 1,
            'locked_amount' => 0,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'symbol' => 'ETH',
            'side' => 'sell',
            'price' => 2000,
            'amount' => 0.4,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', Order::STATUS_OPEN);

        $asset = Asset::where('user_id', $user->id)->where('symbol', 'ETH')->first();
        $this->assertEquals('0.60000000', number_format($asset->amount, 8, '.', ''));
        $this->assertEquals('0.40000000', number_format($asset->locked_amount, 8, '.', ''));
    }

    public function test_buy_matches_first_valid_sell_and_applies_fee(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create([
            'balance' => 200,
        ]);

        // Seller posts a sell order for 1 BTC at 100
        Asset::factory()->for($seller)->create([
            'symbol' => 'BTC',
            'amount' => 1,
            'locked_amount' => 0,
        ]);
        Sanctum::actingAs($seller);
        $sellResponse = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => 100,
            'amount' => 1,
        ]);
        $sellResponse->assertStatus(201);

        // Buyer posts a buy order that should match
        Sanctum::actingAs($buyer);
        $buyResponse = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 110,
            'amount' => 1,
        ]);
        $buyResponse->assertStatus(201);

        $buyOrderId = $buyResponse->json('data.id');
        $sellOrderId = $sellResponse->json('data.id');

        $buyer->refresh();
        $seller->refresh();

        $this->assertEquals(Order::STATUS_FILLED, Order::find($buyOrderId)->status);
        $this->assertEquals(Order::STATUS_FILLED, Order::find($sellOrderId)->status);

        // Buyer balance after paying 110
        $this->assertEquals('90.00000000', number_format($buyer->balance, 8, '.', ''));

        // Seller balance after receiving volume minus 1.5% fee: 110 - 1.65 = 108.35
        $this->assertEquals('108.35000000', number_format($seller->balance, 8, '.', ''));

        // Buyer received the asset
        $buyerAsset = Asset::where('user_id', $buyer->id)->where('symbol', 'BTC')->first();
        $this->assertEquals('1.00000000', number_format($buyerAsset->amount, 8, '.', ''));

        // Seller locked amount released
        $sellerAsset = Asset::where('user_id', $seller->id)->where('symbol', 'BTC')->first();
        $this->assertEquals('0.00000000', number_format($sellerAsset->locked_amount, 8, '.', ''));
        $this->assertEquals('0.00000000', number_format($sellerAsset->amount, 8, '.', ''));
    }

    public function test_matching_broadcasts_order_matched_event(): void
    {
        Event::fake([OrderMatchedEvent::class]);

        $seller = User::factory()->create();
        $buyer = User::factory()->create(['balance' => 200]);

        Asset::factory()->for($seller)->create([
            'symbol' => 'BTC',
            'amount' => 1,
            'locked_amount' => 0,
        ]);

        Sanctum::actingAs($seller);
        $sellResponse = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => 100,
            'amount' => 1,
        ]);
        $sellResponse->assertStatus(201);

        Sanctum::actingAs($buyer);
        $buyResponse = $this->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 110,
            'amount' => 1,
        ]);
        $buyResponse->assertStatus(201);

        Event::assertDispatched(OrderMatchedEvent::class, function ($event) use ($buyer, $seller) {
            $channels = collect($event->broadcastOn())->map(fn($ch) => method_exists($ch, 'name') ? $ch->name() : $ch->name);
            return $channels->contains("private-user.{$buyer->id}")
                && $channels->contains("private-user.{$seller->id}")
                && bccomp($event->amount, 1, 8) === 0
                && $event->buyOrder->symbol === 'BTC'
                && $event->sellOrder->symbol === 'BTC';;
        });
    }
}
