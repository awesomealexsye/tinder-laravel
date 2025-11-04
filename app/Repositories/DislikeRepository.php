<?php

namespace App\Repositories;

use App\Models\Dislike;
use App\Repositories\Contracts\DislikeRepositoryInterface;

class DislikeRepository implements DislikeRepositoryInterface
{
    /**
     * Create a new dislike.
     */
    public function create(array $data): Dislike
    {
        return Dislike::create(array_merge($data, ['created_at' => now()]));
    }

    /**
     * Check if a dislike exists.
     */
    public function exists(int $userId, int $dislikedId): bool
    {
        return Dislike::where('user_id', $userId)
            ->where('disliked_id', $dislikedId)
            ->exists();
    }

    /**
     * Delete a dislike.
     */
    public function delete(int $userId, int $dislikedId): bool
    {
        return Dislike::where('user_id', $userId)
            ->where('disliked_id', $dislikedId)
            ->delete() > 0;
    }

    /**
     * Get IDs of users disliked by a specific user.
     */
    public function getDislikedUserIds(int $userId): array
    {
        return Dislike::where('user_id', $userId)
            ->pluck('disliked_id')
            ->toArray();
    }
}
