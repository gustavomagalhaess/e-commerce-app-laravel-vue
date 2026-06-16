<?php

namespace App\Domains\Catalog\Repositories;

use App\Domains\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function paginate(string $search = '', array $categoryIds = [], int $perPage = 12): LengthAwarePaginator
    {
        return Product::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($categoryIds, fn ($q) => $q->whereHas('categories', fn ($q) => $q->whereIn('id', $categoryIds)))
            ->with(['categories:id,name,slug', 'seller:id,name'])
            ->paginate($perPage);
    }

    public function findById(int $id): Product
    {
        return Product::with(['categories:id,name,slug', 'seller:id,name'])->findOrFail($id);
    }

    public function findBySellerWithPaginate(int $sellerId, int $perPage = 12): LengthAwarePaginator
    {
        return Product::where('seller_id', $sellerId)
            ->with(['categories:id,name,slug'])
            ->paginate($perPage);
    }

    public function create(array $data, array $categoryIds): Product
    {
        $product = Product::create($data);
        $product->categories()->sync($categoryIds);

        return $product->load(['categories:id,name,slug', 'seller:id,name']);
    }

    public function update(Product $product, array $data, array $categoryIds): Product
    {
        $product->update($data);
        $product->categories()->sync($categoryIds);

        return $product->fresh(['categories:id,name,slug', 'seller:id,name']);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
