<?php

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(private ProductRepository $productRepo) {}

    public function create(User $seller, array $data, array $categoryIds, ?UploadedFile $image): Product
    {
        $imagePath = $image ? $image->store('products', 'public') : null;

        return $this->productRepo->create([
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

        return $this->productRepo->update($product, $data, $categoryIds);
    }

    public function delete(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $this->productRepo->delete($product);
    }

    public function myProducts(int $sellerId): LengthAwarePaginator
    {
        return $this->productRepo->findBySellerWithPaginate($sellerId);
    }
}
