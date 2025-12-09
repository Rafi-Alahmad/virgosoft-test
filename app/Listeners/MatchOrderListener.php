<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Services\OrderService;

class MatchOrderListener
{
    public function __construct(
        private readonly OrderService $orders
    ) {
    }

    public function handle(OrderPlacedEvent $event): void
    {
        $order = $event->order->fresh();

        if (!$order || $order->status !== $order::STATUS_OPEN) {
            return;
        }

        try {
            $this->orders->match($order->user, $order);
        } catch (\Throwable $e) {
            // No-op: matching may fail if no counter order; propagate others if needed
        }
    }
}

