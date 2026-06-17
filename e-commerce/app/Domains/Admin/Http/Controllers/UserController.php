<?php

declare(strict_types=1);

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function index(): JsonResponse
    {
        return response()->json($this->userRepository->paginate());
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
        ]);

        $updated = $this->userRepository->update($user, $data);

        return response()->json(['data' => $updated]);
    }
}
