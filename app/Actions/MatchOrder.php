<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderMatched;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Connection;

class MatchOrder
{
    /**
     * MatchOrder constructor.
     */
    public function __construct(private Connection $db)
    {
    }

    public function handle(Order $order)
    {
        return $this->db->transaction(function () use ($order) {
            $order = Order::whereId($order->id)->lockForUpdate()->first();
            $matchedDetails = null;

            if ($order->type === 'buy') {
                $matchedOrder = $this->findMatchForBuyOrder($order);

                if ($matchedOrder !== null) {
                    $matchedDetails = $this->getMatchDetails(buyOrder: $order, sellOrder: $matchedOrder);
                }
            } else {
                $matchedOrder = $this->findMatchForSellOrder($order);
                if ($matchedOrder !== null) {
                    $matchedDetails = $this->getMatchDetails(buyOrder: $matchedOrder, sellOrder: $order);
                }
            }

            if ($matchedOrder !== null && $matchedDetails !== null) {
                $commissionAmount = $matchedOrder->price * $matchedOrder->amount * config('trading.commission_rate');
                $settledAmount = $matchedOrder->price * $matchedOrder->amount;
                $refundAmount = $order->type === 'buy'
                    ? ($order->price - $matchedOrder->price) * $matchedOrder->amount
                    : 0;

                $matchedDetails['seller']->balance += $settledAmount - $commissionAmount;
                $matchedDetails['soldAsset']->locked_amount -= $matchedOrder->amount;

                $matchedDetails['buyer']->balance += $refundAmount;
                $matchedDetails['boughtAsset']->amount += $matchedOrder->amount;

                $matchedDetails['seller']->save();
                $matchedDetails['soldAsset']->save();
                $matchedDetails['buyer']->save();
                $matchedDetails['boughtAsset']->save();

                // Commission is deducted from the seller's USD proceeds.
                // The accrued commission is not persisted separately as it is out of scope for this exercise.

                $order->update(['status' => OrderStatus::FILLED]);
                $matchedOrder->update(['status' => OrderStatus::FILLED]);

                OrderMatched::dispatch($order->refresh());
                OrderMatched::dispatch($matchedOrder->refresh());
            }
        });
    }

    private function findMatchForBuyOrder(Order $order): ?Order
    {
        return Order::query()
            ->where('user_id', '!=', $order->user_id)
            ->where('symbol', $order->symbol)
            ->where('side', 'sell')
            ->where('price', '<=', $order->price)
            ->where('amount', $order->amount)
            ->where('status', OrderStatus::OPEN)
            ->orderBy('price')
            ->orderBy('created_at')
            ->lockForUpdate()
            ->first();
    }

    private function findMatchForSellOrder(Order $order): ?Order
    {
        return Order::query()
            ->where('user_id', '!=', $order->user_id)
            ->where('symbol', $order->symbol)
            ->where('side', 'buy')
            ->where('price', '>=', $order->price)
            ->where('amount', $order->amount)
            ->where('status', OrderStatus::OPEN)
            ->orderByDesc('price')
            ->orderBy('created_at')
            ->lockForUpdate()
            ->first();
    }

    private function getMatchDetails(Order $buyOrder, Order $sellOrder): array
    {
        $buyer = User::whereId($buyOrder->user_id)
            ->lockForUpdate()
            ->firstOrFail();

        $seller = User::whereId($sellOrder->user_id)
            ->lockForUpdate()
            ->firstOrFail();

        $boughtAsset = $buyer->assets()->whereSymbol($buyOrder->symbol)
            ->lockForUpdate()
            ->firstOrNew(['symbol' => $buyOrder->symbol]);

        $soldAsset = $seller->assets()
            ->whereSymbol($sellOrder->symbol)
            ->lockForUpdate()
            ->firstOrFail();

        return [
            'buyer' => $buyer,
            'seller' => $seller,
            'boughtAsset' => $boughtAsset,
            'soldAsset' => $soldAsset,
        ];
    }
}
