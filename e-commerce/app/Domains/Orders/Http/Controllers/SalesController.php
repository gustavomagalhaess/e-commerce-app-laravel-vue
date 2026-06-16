<?php

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Repositories\OrderRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct(private OrderRepository $orderRepo) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->orderRepo->findSalesBySeller($request->user()->id));
    }
}
