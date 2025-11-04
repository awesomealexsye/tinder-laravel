<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function find(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Get recommended users for a given user.
     */
    public function getRecommendedUsers(
        int $userId,
        array $excludeIds,
        array $filters,
        int $perPage
    ): LengthAwarePaginator;

    /**
     * Get users with a specific like count threshold.
     */
    public function getUsersWithLikeCountAbove(int $threshold): iterable;
}
