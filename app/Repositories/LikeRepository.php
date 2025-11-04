<?php

namespace App\Repositories;

use App\Models\Like;
use App\Models\User;
use App\Repositories\Contracts\LikeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LikeRepository implements LikeRepositoryInterface
{
    /**
     * Create a new like.
     */
    public function create(array $data): Like
    {
        return Like::create(array_merge($data, ['created_at' => now()]));
    }

    /**
     * Check if a like exists.
     */
    public function exists(int $likerId, int $likedId): bool
    {
        return Like::where('liker_id', $likerId)
            ->where('liked_id', $likedId)
            ->exists();
    }

    /**
     * Delete a like.
     */
    public function delete(int $likerId, int $likedId): bool
    {
        return Like::where('liker_id', $likerId)
            ->where('liked_id', $likedId)
            ->delete() > 0;
    }

    /**
     * Count likes received by a user.
     */
    public function countLikesReceived(int $userId): int
    {
        return Like::where('liked_id', $userId)->count();
    }

    /**
     * Get IDs of users liked by a specific user.
     */
    public function getLikedUserIds(int $userId): array
    {
        return Like::where('liker_id', $userId)
            ->pluck('liked_id')
            ->toArray();
    }

    /**
     * Get paginated users liked by a specific user.
     */
    public function getLikedUsers(int $userId, int $perPage): LengthAwarePaginator
    {
        return User::query()
            ->with('photos')
            ->select('users.*', 'likes.created_at as liked_at')
            ->join('likes', 'users.id', '=', 'likes.liked_id')
            ->where('likes.liker_id', $userId)
            ->orderBy('likes.created_at', 'desc')
            ->paginate($perPage);
    }
}
