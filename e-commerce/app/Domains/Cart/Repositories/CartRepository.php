<?php

namespace App\Domains\Cart\Repositories;

use App\Domains\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CartRepository
{
    public function getByUser(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->with(['product:id,seller_id,name,price,image_path'])
            ->get();
    }

    public function findForUser(int $cartItemId, int $userId): CartItem
    {
        return CartItem::where('id', $cartItemId)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function upsert(int $userId, int $productId, int $quantity): CartItem
    {
        $item = CartItem::firstOrNew(['user_id' => $userId, 'product_id' => $productId]);
        $item->quantity = $item->exists ? $item->quantity + $quantity : $quantity;
        $item->save();

        return $item->load('product:id,seller_id,name,price,image_path');
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => $quantity]);

        return $item->fresh('product:id,seller_id,name,price,image_path');
    }

    public function delete(CartItem $item): void
    {
        $item->delete();
    }

    public function clearForUser(int $userId): void
    {
        CartItem::where('user_id', $userId)->delete();
    }
}
