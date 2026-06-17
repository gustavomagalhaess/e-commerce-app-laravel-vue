<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Repositories\Contracts\ProductRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(private readonly ProductRepositoryInterface $productRepository) {}

    public function paginate(string $search = '', array $categoryIds = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->productRepository->paginate($search, $categoryIds, $perPage);
    }

    public function findById(int $id): Product
    {
        return $this->productRepository->findById($id);
    }

    public function create(User $seller, array $data, array $categoryIds, ?UploadedFile $image): Product
    {
        $imagePath = $image ? $image->store('products', 'public') : null;

        return $this->productRepository->create([
            'seller_id' => $seller->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image_path' => $imagePath,
        ], $categoryIds);
    }

    public function update(Product $product, array $data, array $categoryIds, ?UploadedFile $image): Product
    {
        if ($image) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $image->store('products', 'public');
        }

        return $this->productRepository->update($product, $data, $categoryIds);
    }

    public function delete(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $this->productRepository->delete($product);
    }

    public function myProducts(int $sellerId): LengthAwarePaginator
    {
        return $this->productRepository->findBySellerWithPaginate($sellerId);
    }
}
