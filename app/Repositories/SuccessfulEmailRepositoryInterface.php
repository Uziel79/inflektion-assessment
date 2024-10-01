<?php

namespace App\Repositories;

use App\Models\SuccessfulEmail;
use Illuminate\Pagination\LengthAwarePaginator;

interface SuccessfulEmailRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): SuccessfulEmail;
    public function find(int $id): ?SuccessfulEmail;
    public function update(SuccessfulEmail $successfulEmail, array $data): bool;
    public function delete(SuccessfulEmail $successfulEmail): bool;
}
