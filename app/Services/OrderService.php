<?php

namespace App\Services;

use App\Events\OrderPlacedEvent;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function listOpen(?string $symbol, int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->open()
            ->forSymbol($symbol)
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    public function place(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            if ($data['side'] === 'buy') {
                $cost = bcmul($data['price'], $data['amount'], 8);

                if (bccomp($user->balance, $cost, 8) < 0) {
                    throw ValidationException::withMessages([
                        'balance' => ['Insufficient balance for buy order.'],
                    ]);
                }

                $user->balance = bcsub($user->balance, $cost, 8);
                $user->save();
            } else {
                $asset = Asset::firstOrCreate(
                    ['user_id' => $user->id, 'symbol' => $data['symbol']],
                    ['amount' => 0, 'locked_amount' => 0]
                );

                if (bccomp($asset->amount, $data['amount'], 8) < 0) {
                    throw ValidationException::withMessages([
                        'amount' => ['Insufficient asset balance for sell order.'],
                    ]);
                }

                $asset->amount = bcsub($asset->amount, $data['amount'], 8);
                $asset->locked_amount = bcadd($asset->locked_amount, $data['amount'], 8);
                $asset->save();
            }

            $order = Order::create([
                'user_id' => $user->id,
                'symbol' => $data['symbol'],
                'side' => $data['side'],
                'price' => $data['price'],
                'amount' => $data['amount'],
                'status' => Order::STATUS_OPEN,
            ]);

            OrderPlacedEvent::dispatch($order);

            return $order;
        });
    }


    public function match(User $user, Order $order): Order
    {
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to match this order.');
        }

        if ($order->status !== Order::STATUS_OPEN) {
            throw ValidationException::withMessages([
                'status' => ['Only open orders can be matched.'],
            ]);
        }

        $counter = $this->findCounterOrder($order);

        if (!$counter) {
            throw ValidationException::withMessages([
                'match' => ['No valid counter order found.'],
            ]);
        }

        return DB::transaction(function () use ($order, $counter) {
            $amount = $order->amount;

            // use the price of the order that is being matched
            $price = $order->price;
            $volume = bcmul($price, $amount, 8);
            $fee = bcmul($volume, '0.015', 8); // 1.5% fee

            if ($order->side === 'buy') {
                // buyer is $order, seller is $counter
                $this->applySellSettlement($counter, $volume, $fee);
                $this->applyBuySettlement($order, $amount, $counter->symbol);
            } else {
                // seller is $order, buyer is $counter
                $this->applySellSettlement($order, $volume, $fee);
                $this->applyBuySettlement($counter, $amount, $order->symbol);
            }

            $order->status = Order::STATUS_FILLED;
            $order->save();

            $counter->status = Order::STATUS_FILLED;
            $counter->save();

            return $order->fresh();
        });
    }

    protected function findCounterOrder(Order $order): ?Order
    {
        $query = Order::query()
            ->open()
            ->forSymbol($order->symbol)
            ->where('id', '!=', $order->id)
            ->where('amount', $order->amount);

        if ($order->side === 'buy') {
            $query->where('side', 'sell')
                ->where('price', '<=', $order->price)
                ->orderBy('price')
                ->orderBy('created_at');
        } else {
            $query->where('side', 'buy')
                ->where('price', '>=', $order->price)
                ->orderBy('price', 'desc')
                ->orderBy('created_at');
        }

        return $query->first();
    }

    protected function applySellSettlement(Order $sellOrder, string $volume, string $fee): void
    {
        $seller = $sellOrder->user;
        $asset = Asset::firstOrCreate(
            ['user_id' => $seller->id, 'symbol' => $sellOrder->symbol],
            ['amount' => 0, 'locked_amount' => 0]
        );

        // release locked amount (exact match amount)
        $asset->locked_amount = bcsub($asset->locked_amount, $sellOrder->amount, 8);
        $asset->save();

        // credit seller balance minus fee
        $seller->balance = bcadd($seller->balance, bcsub($volume, $fee, 8), 8);
        $seller->save();
    }

    protected function applyBuySettlement(Order $buyOrder, string $amount, string $symbol): void
    {
        $buyer = $buyOrder->user;
        $asset = Asset::firstOrCreate(
            ['user_id' => $buyer->id, 'symbol' => $symbol],
            ['amount' => 0, 'locked_amount' => 0]
        );

        $asset->amount = bcadd($asset->amount, $amount, 8);
        $asset->save();
    }

    public function cancel(User $user, Order $order): Order
    {
        if ($order->user_id !== $user->id) {
            abort(403, 'You do not have permission to cancel this order.');
        }

        if ($order->status !== Order::STATUS_OPEN) {
            throw ValidationException::withMessages([
                'status' => ['Only open orders can be cancelled.'],
            ]);
        }

        return DB::transaction(function () use ($user, $order) {
            if ($order->side === 'buy') {
                $cost = bcmul($order->price, $order->amount, 8);
                $user->balance = bcadd($user->balance, $cost, 8);
                $user->save();
            } else {
                $asset = Asset::firstOrCreate(
                    ['user_id' => $user->id, 'symbol' => $order->symbol],
                    ['amount' => 0, 'locked_amount' => 0]
                );

                $asset->locked_amount = bcsub($asset->locked_amount, $order->amount, 8);
                $asset->amount = bcadd($asset->amount, $order->amount, 8);
                $asset->save();
            }

            $order->status = Order::STATUS_CANCELLED;
            $order->save();

            return $order->fresh();
        });
    }
}

