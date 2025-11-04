<?php

namespace App\Repositories\Contracts;

use App\Models\Dislike;

interface DislikeRepositoryInterface
{
    /**
     * Create a new dislike.
     */
    public function create(array $data): Dislike;

    /**
     * Check if a dislike exists.
     */
    public function exists(int $userId, int $dislikedId): bool;

    /**
     * Delete a dislike.
     */
    public function delete(int $userId, int $dislikedId): bool;

    /**
     * Get IDs of users disliked by a specific user.
     */
    public function getDislikedUserIds(int $userId): array;
}
