<?php

namespace App\Repositories;

use App\Models\SuccessfulEmail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class SuccessfulEmailRepository implements SuccessfulEmailRepositoryInterface
{
    public function getAllPaginated(int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('successful_email.per_page');
        $cacheTTL = config('successful_email.cache_ttl');

        return Cache::remember('successful_emails_page_' . request('page', 1), $cacheTTL, function () use ($perPage) {
            return SuccessfulEmail::paginate($perPage);
        });
    }

    public function create(array $data): SuccessfulEmail
    {
        return SuccessfulEmail::create($data);
    }

    public function find(int $id): ?SuccessfulEmail
    {
        return SuccessfulEmail::find($id);
    }

    public function update(SuccessfulEmail $successfulEmail, array $data): bool
    {
        return $successfulEmail->update($data);
    }

    public function delete(SuccessfulEmail $successfulEmail): bool
    {
        return $successfulEmail->delete();
    }
}
