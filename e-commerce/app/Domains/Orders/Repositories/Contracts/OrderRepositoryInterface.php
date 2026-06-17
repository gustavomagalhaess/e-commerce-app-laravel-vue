<?php

declare(strict_types=1);

namespace App\Domains\Orders\Repositories\Contracts;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findById(int $id): Order;

    public function findByBuyer(int $buyerId): LengthAwarePaginator;

    public function findSalesBySeller(int $sellerId): LengthAwarePaginator;

    public function updateStatus(Order $order, string $status): void;

    public function createItem(array $data): OrderItem;

    public function updateTotal(Order $order, string $total): void;
}
