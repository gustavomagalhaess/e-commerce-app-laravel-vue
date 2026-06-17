<?php

declare(strict_types=1);

namespace App\Domains\Orders\Services;

use App\Domains\Cart\Repositories\Contracts\CartRepositoryInterface;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartRepositoryInterface $cartRepository,
    ) {}

    public function findSalesBySeller(int $sellerId): LengthAwarePaginator
    {
        return $this->orderRepository->findSalesBySeller($sellerId);
    }

    public function findByBuyer(int $buyerId): LengthAwarePaginator
    {
        return $this->orderRepository->findByBuyer($buyerId);
    }

    public function findById(int $id): Order
    {
        return $this->orderRepository->findById($id);
    }

    public function initiateCheckout(int $buyerId, string $paymentMethod): Order
    {
        $cartItems = $this->cartRepository->getByUser($buyerId);

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty.'],
            ]);
        }

        return $this->orderRepository->create([
            'buyer_id' => $buyerId,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'total' => 0,
        ]);
    }

    public function buildOrderFromCart(int $orderId, int $buyerId): void
    {
        $order = Order::findOrFail($orderId);

        try {
            $cartItems = $this->cartRepository->getByUser($buyerId);
            $total = '0';

            foreach ($cartItems as $cartItem) {
                $lineTotal = bcmul((string) $cartItem->product->price, (string) $cartItem->quantity, 2);
                $total = bcadd($total, $lineTotal, 2);

                $this->orderRepository->createItem([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'seller_id' => $cartItem->product->seller_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_purchase' => $cartItem->product->price,
                ]);
            }

            $this->orderRepository->updateTotal($order, $total);
            $this->cartRepository->clearForUser($buyerId);
            $this->orderRepository->updateStatus($order, 'paid');
        } catch (\Throwable $e) {
            $this->orderRepository->updateStatus($order, 'failed');
            throw $e;
        }
    }
}
