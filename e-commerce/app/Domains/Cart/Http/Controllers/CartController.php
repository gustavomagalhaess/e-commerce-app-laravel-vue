<?php

namespace App\Domains\Cart\Http\Controllers;

use App\Domains\Cart\Services\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->cartService->getCart($request->user()->id));
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = $this->cartService->addItem($request->user(), $data['product_id'], $data['quantity']);

        return response()->json(['data' => $item], 201);
    }

    public function updateItem(Request $request, int $cartItemId): JsonResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $item = $this->cartService->getItemForUser($cartItemId, $request->user()->id);
        $item = $this->cartService->updateItem($item, $data['quantity']);

        return response()->json(['data' => $item]);
    }

    public function removeItem(Request $request, int $cartItemId): Response
    {
        $item = $this->cartService->getItemForUser($cartItemId, $request->user()->id);
        $this->cartService->removeItem($item);

        return response()->noContent();
    }

    public function clear(Request $request): Response
    {
        $this->cartService->clearCart($request->user()->id);

        return response()->noContent();
    }
}
