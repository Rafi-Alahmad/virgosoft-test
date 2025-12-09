<?php

namespace Tests\Unit;

use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    public function test_place_buy_deducts_balance_and_opens(): void
    {
        Event::fake();

        $user = User::factory()->create(['balance' => 500]);

        $order = $this->service->place($user, [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 100,
            'amount' => 2,
        ]);

        $this->assertEquals(Order::STATUS_OPEN, $order->status);
        $this->assertEquals('300.00000000', number_format($user->fresh()->balance, 8, '.', ''));
    }

    public function test_place_sell_locks_asset_and_opens(): void
    {
        Event::fake();

        $user = User::factory()->create();
        Asset::factory()->for($user)->create([
            'symbol' => 'ETH',
            'amount' => 1,
            'locked_amount' => 0,
        ]);

        $order = $this->service->place($user, [
            'symbol' => 'ETH',
            'side' => 'sell',
            'price' => 1500,
            'amount' => 0.4,
        ]);

        $asset = Asset::where('user_id', $user->id)->where('symbol', 'ETH')->first();

        $this->assertEquals(Order::STATUS_OPEN, $order->status);
        $this->assertEquals('0.60000000', number_format($asset->amount, 8, '.', ''));
        $this->assertEquals('0.40000000', number_format($asset->locked_amount, 8, '.', ''));
    }

    public function test_match_buy_with_sell_applies_fee_and_sets_filled(): void
    {
        Event::fake();

        $seller = User::factory()->create();
        $buyer = User::factory()->create(['balance' => 200]);

        Asset::factory()->for($seller)->create([
            'symbol' => 'BTC',
            'amount' => 1,
            'locked_amount' => 0,
        ]);

        $sellOrder = $this->service->place($seller, [
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => 100,
            'amount' => 1,
        ]);

        $buyOrder = $this->service->place($buyer, [
            'symbol' => 'BTC',
            'side' => 'buy',
            'price' => 110,
            'amount' => 1,
        ]);

        $matched = $this->service->match($buyer, $buyOrder);

        $seller->refresh();
        $buyer->refresh();

        $this->assertEquals(Order::STATUS_FILLED, $matched->status);
        $this->assertEquals(Order::STATUS_FILLED, $sellOrder->fresh()->status);

        // Buyer paid 110 total
        $this->assertEquals('90.00000000', number_format($buyer->balance, 8, '.', ''));

        // Seller received 110 - 1.5% = 108.35
        $this->assertEquals('108.35000000', number_format($seller->balance, 8, '.', ''));

        $buyerAsset = Asset::where('user_id', $buyer->id)->where('symbol', 'BTC')->first();
        $this->assertEquals('1.00000000', number_format($buyerAsset->amount, 8, '.', ''));

        $sellerAsset = Asset::where('user_id', $seller->id)->where('symbol', 'BTC')->first();
        $this->assertEquals('0.00000000', number_format($sellerAsset->locked_amount, 8, '.', ''));
        $this->assertEquals('0.00000000', number_format($sellerAsset->amount, 8, '.', ''));
    }
}

