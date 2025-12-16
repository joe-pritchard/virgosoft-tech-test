<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderCreateRequest;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateBuyOrder
{
    /**
     * CreateBuyOrder constructor.
     */
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

        if ($input['side'] !== 'buy') {
            throw ValidationException::withMessages(['side' => 'Invalid side for buy order']);
        }

        return $this->db->transaction(function () use ($user, $input) {
            /** @var User $lockedUser */
            $lockedUser = User::whereId($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            throw_unless(
                $lockedUser->canAfford($input['price'] * $input['amount']),
                ValidationException::withMessages(['balance' => 'Insufficient balance'])
            );

            $lockedUser->balance -= $input['price'] * $input['amount'];
            $lockedUser->save();

            return $lockedUser->orders()->create([
                'symbol' => $input['symbol'],
                'side' => 'buy',
                'price' => $input['price'],
                'amount' => $input['amount'],
                'status' => OrderStatus::OPEN,
            ]);
        });
    }
}
