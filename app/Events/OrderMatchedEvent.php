<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatchedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $buyOrder,
        public Order $sellOrder,
        public $price,
        public $amount,
        public $fee
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->buyOrder->user_id),
            new PrivateChannel('user.'.$this->sellOrder->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'symbol' => $this->buyOrder->symbol,
            'price' => $this->price,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'buy_order' => [
                'id' => $this->buyOrder->id,
                'status' => $this->buyOrder->status,
            ],
            'sell_order' => [
                'id' => $this->sellOrder->id,
                'status' => $this->sellOrder->status,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'order-matched';
    }
}

