<?php

namespace App\Domains\Cart\Services;

use App\Domains\Cart\Models\CartItem;
use App\Domains\Cart\Repositories\CartRepository;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        private CartRepository $cartRepo,
        private ProductRepository $productRepo,
    ) {}

    public function getCart(int $userId): array
    {
        $items = $this->cartRepo->getByUser($userId);
        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return [
            'data' => $items,
            'total' => number_format($total, 2, '.', ''),
            'item_count' => $items->sum('quantity'),
        ];
    }

    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        $product = $this->productRepo->findById($productId);

        if ($product->seller_id === $user->id) {
            throw ValidationException::withMessages([
                'product_id' => ['You cannot add your own product to the cart.'],
            ])->status(422);
        }

        return $this->cartRepo->upsert($user->id, $productId, $quantity);
    }

    public function getItemForUser(int $cartItemId, int $userId): CartItem
    {
        return $this->cartRepo->findForUser($cartItemId, $userId);
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        return $this->cartRepo->updateQuantity($item, $quantity);
    }

    public function removeItem(CartItem $item): void
    {
        $this->cartRepo->delete($item);
    }

    public function clearCart(int $userId): void
    {
        $this->cartRepo->clearForUser($userId);
    }
}
