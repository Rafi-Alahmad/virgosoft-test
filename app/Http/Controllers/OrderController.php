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

    public function listOpen(Request $request)
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

    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'nullable', 'in:1,2,3,null'],
            'side' => ['sometimes', 'nullable', 'in:buy,sell,null'],
            'symbol' => ['sometimes', 'nullable', 'string', 'max:10'],
        ]);

        $validated = array_filter($validated, function ($value) {
            return $value !== null && $value !== 'null';
        });

        return OrderResource::collection(
            $this->orders->listOrders(
                $request->integer('per_page', 50),
                $validated['status'] ?? null,
                $validated['side'] ?? null,
                $validated['symbol'] ?? null,
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
