<?php

namespace App\Domains\Catalog\Policies;

use App\Domains\Catalog\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->seller_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->seller_id || $user->isAdmin();
    }
}
