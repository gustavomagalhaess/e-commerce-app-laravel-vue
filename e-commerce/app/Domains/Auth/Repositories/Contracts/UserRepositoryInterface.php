<?php

declare(strict_types=1);

namespace App\Domains\Auth\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(): LengthAwarePaginator;

    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function update(User $user, array $data): User;
}
