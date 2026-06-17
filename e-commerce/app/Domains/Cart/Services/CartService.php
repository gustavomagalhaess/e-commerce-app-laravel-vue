<?php

declare(strict_types=1);

namespace App\Domains\Cart\Services;

use App\Domains\Cart\Models\CartItem;
use App\Domains\Cart\Repositories\Contracts\CartRepositoryInterface;
use App\Domains\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function getCart(int $userId): array
    {
        $items = $this->cartRepository->getByUser($userId);
        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return [
            'data' => $items,
            'total' => number_format($total, 2, '.', ''),
            'item_count' => $items->sum('quantity'),
        ];
    }

    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        $product = $this->productRepository->findById($productId);

        if ($product->seller_id === $user->id) {
            throw ValidationException::withMessages([
                'product_id' => ['You cannot add your own product to the cart.'],
            ])->status(422);
        }

        return $this->cartRepository->upsert($user->id, $productId, $quantity);
    }

    public function getItemForUser(int $cartItemId, int $userId): CartItem
    {
        return $this->cartRepository->findForUser($cartItemId, $userId);
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        return $this->cartRepository->updateQuantity($item, $quantity);
    }

    public function removeItem(CartItem $item): void
    {
        $this->cartRepository->delete($item);
    }

    public function clearCart(int $userId): void
    {
        $this->cartRepository->clearForUser($userId);
    }
}
