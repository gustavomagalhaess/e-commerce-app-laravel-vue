<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Repositories\CategoryRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private CategoryRepository $categoryRepo) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:categories,slug'],
        ]);

        return response()->json(['data' => $this->categoryRepo->create($data)], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:categories,slug,'.$category->id],
        ]);

        return response()->json(['data' => $this->categoryRepo->update($category, $data)]);
    }

    public function destroy(Category $category): Response
    {
        $this->categoryRepo->delete($category);

        return response()->noContent();
    }
}
