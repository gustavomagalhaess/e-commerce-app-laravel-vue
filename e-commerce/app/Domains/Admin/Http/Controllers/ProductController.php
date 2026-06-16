<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Domains\Catalog\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
        private ProductService $productService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->productRepo->paginate());
    }

    public function destroy(Product $product): Response
    {
        $this->productService->delete($product);

        return response()->noContent();
    }
}
