<?php

declare(strict_types=1);

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Repositories\Contracts\CategoryRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:categories,slug'],
        ]);

        return response()->json(['data' => $this->categoryRepository->create($data)], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:categories,slug,'.$category->id],
        ]);

        return response()->json(['data' => $this->categoryRepository->update($category, $data)]);
    }

    public function destroy(Category $category): Response
    {
        $this->categoryRepository->delete($category);

        return response()->noContent();
    }
}
