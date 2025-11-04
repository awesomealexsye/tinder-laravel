<?php

namespace App\Services;

use App\Models\Dislike;
use App\Repositories\Contracts\DislikeRepositoryInterface;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class DislikeService
{
    public function __construct(
        private DislikeRepositoryInterface $dislikeRepository,
        private LikeRepositoryInterface $likeRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Dislike a user.
     *
     * @throws \Exception
     */
    public function dislikeUser(int $userId, int $dislikedId): Dislike
    {
        // Validate that user is not disliking themselves
        if ($userId === $dislikedId) {
            throw new \Exception('You cannot dislike yourself');
        }

        // Check if the disliked user exists
        $dislikedUser = $this->userRepository->find($dislikedId);
        if (! $dislikedUser) {
            throw new \Exception('User not found');
        }

        // Check if already disliked
        if ($this->dislikeRepository->exists($userId, $dislikedId)) {
            throw new \Exception('You have already disliked this user');
        }

        // Remove like if exists (user changed their mind)
        if ($this->likeRepository->exists($userId, $dislikedId)) {
            $this->likeRepository->delete($userId, $dislikedId);
        }

        // Create the dislike
        return $this->dislikeRepository->create([
            'user_id' => $userId,
            'disliked_id' => $dislikedId,
        ]);
    }
}
