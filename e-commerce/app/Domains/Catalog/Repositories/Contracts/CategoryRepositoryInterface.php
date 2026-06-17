<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Repositories\Contracts;

use App\Domains\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): void;
}
