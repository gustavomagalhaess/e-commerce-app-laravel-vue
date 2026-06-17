<?php

declare(strict_types=1);

namespace App\Domains\Orders\Http\Controllers;

use App\Domains\Orders\Jobs\ProcessOrder;
use App\Domains\Orders\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', 'in:credit_card,pix,boleto'],
        ]);

        $order = $this->orderService->initiateCheckout($request->user()->id, $data['payment_method']);

        ProcessOrder::dispatch($order->id, $request->user()->id);

        return response()->json(['data' => $order], 202);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->orderService->findByBuyer($request->user()->id));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = $this->orderService->findById($id);

        abort_if($order->buyer_id !== $request->user()->id && ! $request->user()->isAdmin(), 403);

        return response()->json(['data' => $order]);
    }
}
