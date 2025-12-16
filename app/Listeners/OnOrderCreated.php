<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Actions\MatchOrder;
use App\Events\OrderCreated;

class OnOrderCreated
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly MatchOrder $matchOrder)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $this->matchOrder->handle($event->order);
    }
}
