<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateBuyOrder;
use App\Actions\CreateSellOrder;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        OrderCreateRequest $request,
        CreateSellOrder $createSellOrder,
        CreateBuyOrder $createBuyOrder
    ): OrderResource {
        $input = $request->validated();
        if ($input['side'] === 'buy') {
            $order = $createBuyOrder->handle($request);
        }

        if ($input['side'] === 'sell') {
            $order = $createSellOrder->handle($request);
        }

        return OrderResource::make($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
