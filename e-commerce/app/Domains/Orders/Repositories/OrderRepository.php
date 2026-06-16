<?php

namespace App\Domains\Orders\Repositories;

use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findById(int $id): Order
    {
        return Order::with(['items.product:id,name', 'items.seller:id,name'])->findOrFail($id);
    }

    public function findByBuyer(int $buyerId): LengthAwarePaginator
    {
        return Order::where('buyer_id', $buyerId)
            ->latest()
            ->paginate(15);
    }

    public function findSalesBySeller(int $sellerId): LengthAwarePaginator
    {
        return OrderItem::where('seller_id', $sellerId)
            ->with(['order:id,buyer_id,status,created_at', 'order.buyer:id,name', 'product:id,name'])
            ->latest()
            ->paginate(15);
    }

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => $status]);
    }

    public function createItem(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function updateTotal(Order $order, string $total): void
    {
        $order->update(['total' => $total]);
    }
}
