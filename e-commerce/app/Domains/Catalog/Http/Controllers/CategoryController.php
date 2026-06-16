<?php

namespace App\Domains\Catalog\Http\Controllers;

use App\Domains\Catalog\Repositories\CategoryRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private CategoryRepository $categoryRepo) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->categoryRepo->all()]);
    }
}
