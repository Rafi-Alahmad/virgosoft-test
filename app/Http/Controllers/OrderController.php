<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'symbol' => ['sometimes', 'string', 'max:10'],
        ]);

        return OrderResource::collection(
            $this->orders->listOpen(
                $validated['symbol'] ?? null,
                $request->integer('per_page', 10)
            )
        );
    }

    public function store(StoreOrderRequest $request)
    {
        return new OrderResource(
            $this->orders->place($request->user(), $request->validated())
        );
    }

    /**
     * Cancel an open order and release locked funds/assets.
     */
    public function cancel(Request $request, Order $order)
    {
        return new OrderResource(
            $this->orders->cancel($request->user(), $order)
        );
    }

    /**
     * Trigger matching for an order with the first valid counter order.
     */
    public function match(Request $request, Order $order)
    {
        return new OrderResource(
            $this->orders->match($request->user(), $order)
        );
    }
}
