<?php

namespace App\Domains\Orders\Services;

use App\Domains\Cart\Repositories\CartRepository;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Repositories\OrderRepository;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepo,
        private CartRepository $cartRepo,
    ) {}

    public function initiateCheckout(int $buyerId, string $paymentMethod): Order
    {
        $cartItems = $this->cartRepo->getByUser($buyerId);

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Your cart is empty.'],
            ]);
        }

        return $this->orderRepo->create([
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
            $cartItems = $this->cartRepo->getByUser($buyerId);
            $total = '0';

            foreach ($cartItems as $cartItem) {
                $lineTotal = bcmul((string) $cartItem->product->price, (string) $cartItem->quantity, 2);
                $total = bcadd($total, $lineTotal, 2);

                $this->orderRepo->createItem([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'seller_id' => $cartItem->product->seller_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_purchase' => $cartItem->product->price,
                ]);
            }

            $this->orderRepo->updateTotal($order, $total);
            $this->cartRepo->clearForUser($buyerId);
            $this->orderRepo->updateStatus($order, 'paid');
        } catch (\Throwable $e) {
            $this->orderRepo->updateStatus($order, 'failed');
            throw $e;
        }
    }
}
