<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Repositories\Contracts;

use App\Domains\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(string $search = '', array $categoryIds = [], int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): Product;

    public function findBySellerWithPaginate(int $sellerId, int $perPage = 12): LengthAwarePaginator;

    public function create(array $data, array $categoryIds): Product;

    public function update(Product $product, array $data, array $categoryIds): Product;

    public function delete(Product $product): void;
}
