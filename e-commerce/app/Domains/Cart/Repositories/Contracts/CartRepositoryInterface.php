<?php

declare(strict_types=1);

namespace App\Domains\Cart\Repositories\Contracts;

use App\Domains\Cart\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    public function getByUser(int $userId): Collection;

    public function findForUser(int $cartItemId, int $userId): CartItem;

    public function upsert(int $userId, int $productId, int $quantity): CartItem;

    public function updateQuantity(CartItem $item, int $quantity): CartItem;

    public function delete(CartItem $item): void;

    public function clearForUser(int $userId): void;
}
