<?php

namespace App\Repositories\Contracts;

use App\Models\Like;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LikeRepositoryInterface
{
    /**
     * Create a new like.
     */
    public function create(array $data): Like;

    /**
     * Check if a like exists.
     */
    public function exists(int $likerId, int $likedId): bool;

    /**
     * Delete a like.
     */
    public function delete(int $likerId, int $likedId): bool;

    /**
     * Count likes received by a user.
     */
    public function countLikesReceived(int $userId): int;

    /**
     * Get IDs of users liked by a specific user.
     */
    public function getLikedUserIds(int $userId): array;

    /**
     * Get paginated users liked by a specific user.
     */
    public function getLikedUsers(int $userId, int $perPage): LengthAwarePaginator;
}
