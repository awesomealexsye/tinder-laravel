<?php

namespace App\Services;

use App\Events\UserReached50LikesEvent;
use App\Models\Like;
use App\Repositories\Contracts\DislikeRepositoryInterface;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LikeService
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private DislikeRepositoryInterface $dislikeRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Like a user.
     *
     * @throws \Exception
     */
    public function likeUser(int $likerId, int $likedId): Like
    {
        // Validate that user is not liking themselves
        if ($likerId === $likedId) {
            throw new \Exception('You cannot like yourself');
        }

        // Check if the liked user exists
        $likedUser = $this->userRepository->find($likedId);
        if (! $likedUser) {
            throw new \Exception('User not found');
        }

        // Check if already liked
        if ($this->likeRepository->exists($likerId, $likedId)) {
            throw new \Exception('You have already liked this user');
        }

        // Remove dislike if exists (user changed their mind)
        if ($this->dislikeRepository->exists($likerId, $likedId)) {
            $this->dislikeRepository->delete($likerId, $likedId);
        }

        // Create the like
        $like = $this->likeRepository->create([
            'liker_id' => $likerId,
            'liked_id' => $likedId,
        ]);

        // Check if user reached 50 likes threshold
        $likeCount = $this->likeRepository->countLikesReceived($likedId);
        if ($likeCount >= 50) {
            // Dispatch event only if they haven't been notified before
            if ($likedUser->adminNotifications()->count() === 0) {
                event(new UserReached50LikesEvent($likedUser, $likeCount));
            }
        }

        return $like;
    }

    /**
     * Get users liked by a specific user.
     */
    public function getLikedUsers(int $userId, int $perPage): LengthAwarePaginator
    {
        return $this->likeRepository->getLikedUsers($userId, $perPage);
    }
}
