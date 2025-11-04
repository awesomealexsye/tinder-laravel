<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Get recommended users for a given user.
     */
    public function getRecommendedUsers(
        int $userId,
        array $excludeIds,
        array $filters,
        int $perPage
    ): LengthAwarePaginator {
        $query = User::query()
            ->with('photos')
            ->where('id', '!=', $userId)
            ->where('is_active', true)
            ->whereNotIn('id', $excludeIds);

        // Apply gender filter
        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Apply age filters
        if (isset($filters['min_age'])) {
            $query->where('age', '>=', $filters['min_age']);
        }

        if (isset($filters['max_age'])) {
            $query->where('age', '<=', $filters['max_age']);
        }

        return $query->inRandomOrder()->paginate($perPage);
    }

    /**
     * Get users with a specific like count threshold.
     */
    public function getUsersWithLikeCountAbove(int $threshold): iterable
    {
        return User::query()
            ->withCount('likesReceived')
            ->having('likes_received_count', '>=', $threshold)
            ->whereDoesntHave('adminNotifications')
            ->get();
    }
}
