<?php

namespace App\Domains\Orders\Jobs;

use App\Domains\Orders\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $orderId,
        public readonly int $buyerId,
    ) {}

    public function handle(OrderService $orderService): void
    {
        $orderService->buildOrderFromCart($this->orderId, $this->buyerId);
    }

    public function queue(): string
    {
        return 'orders';
    }
}
