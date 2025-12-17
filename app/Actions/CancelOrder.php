<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Connection;
use Illuminate\Validation\ValidationException;

class CancelOrder
{
    public function __construct(private Connection $db)
    {
    }

    public function handle(Order $order): Order
    {
        return $this->db->transaction(function () use ($order) {
            $order = Order::where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $user = $order->user()->lockForUpdate()->firstOrFail();

            throw_unless(
                $order->status === OrderStatus::OPEN,
                ValidationException::withMessages(['order_id' => 'Only open orders can be cancelled'])
            );

            if ($order->side === 'buy') {
                $user->balance += $order->price * $order->amount;
                $user->save();
            }

            $order->status = OrderStatus::CANCELLED;
            $order->save();

            return $order;
        });
    }
}
