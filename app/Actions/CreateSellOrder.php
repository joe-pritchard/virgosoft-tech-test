<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderCreateRequest;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateSellOrder
{
    public function __construct(private Connection $db)
    {
    }

    /**
     * @throws ValidationException
     * @throws Throwable
     */
    public function handle(OrderCreateRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $input = $request->validated();

        if ($input['side'] !== 'sell') {
            throw ValidationException::withMessages(['side' => 'Invalid side for sell order']);
        }

        return $this->db->transaction(function () use ($user, $input) {
            /** @var Asset $lockedAsset */
            $lockedAsset = $user->assets()
                ->whereSymbol($input['symbol'])
                ->lockForUpdate()
                ->firstOrFail();

            throw_unless(
                $lockedAsset->amount >= $input['amount'],
                ValidationException::withMessages(['amount' => 'Insufficient asset amount to place sell order'])
            );

            $lockedAsset->amount -= $input['amount'];
            $lockedAsset->locked_amount += $input['amount'];
            $lockedAsset->save();

            return $user->orders()->create([
                'symbol' => $input['symbol'],
                'side' => 'sell',
                'price' => $input['price'],
                'amount' => $input['amount'],
                'status' => OrderStatus::OPEN,
            ]);
        });
    }
}
