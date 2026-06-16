<?php

namespace App\Domains\Catalog\Http\Controllers;

use App\Domains\Catalog\Http\Requests\StoreProductRequest;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Repositories\ProductRepository;
use App\Domains\Catalog\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
        private ProductService $productService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->productRepo->paginate(
            search: $request->string('search')->toString(),
            categoryIds: (array) $request->input('category', []),
        );

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productRepo->findById($id);

        return response()->json(['data' => $product]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create(
            seller: $request->user(),
            data: $request->validated(),
            categoryIds: $request->validated('category_ids'),
            image: $request->file('image'),
        );

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = $this->productRepo->findById($id);
        Gate::authorize('update', $product);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $product = $this->productService->update(
            product: $product,
            data: $data,
            categoryIds: $data['category_ids'] ?? [],
            image: $request->file('image'),
        );

        return response()->json(['data' => $product]);
    }

    public function destroy(int $id): Response
    {
        $product = $this->productRepo->findById($id);
        Gate::authorize('delete', $product);
        $this->productService->delete($product);

        return response()->noContent();
    }

    public function myProducts(Request $request): JsonResponse
    {
        return response()->json($this->productService->myProducts($request->user()->id));
    }
}
