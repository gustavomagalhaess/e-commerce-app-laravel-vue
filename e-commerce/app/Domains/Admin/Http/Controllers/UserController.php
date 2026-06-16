<?php

namespace App\Domains\Admin\Http\Controllers;

use App\Domains\Auth\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepo) {}

    public function index(): JsonResponse
    {
        return response()->json(User::paginate(20));
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:admin,customer'],
        ]);

        $updated = $this->userRepo->update($user, $data);

        return response()->json(['data' => $updated]);
    }
}
