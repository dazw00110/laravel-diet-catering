<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LoyaltyProgramService;

class OrderObserver
{
    public function created(Order $order): void
    {
        //
    }

    public function updated(Order $order): void
    {
        if ($order->status === 'completed') {
            app(LoyaltyProgramService::class)->evaluate($order->user);
        }
    }

    public function deleted(Order $order): void
    {
        //
    }

    public function restored(Order $order): void
    {
        //
    }

    public function forceDeleted(Order $order): void
    {
        //
    }
}
