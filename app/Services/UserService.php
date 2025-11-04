<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\DislikeRepositoryInterface;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Strategies\RecommendationStrategyInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LikeRepositoryInterface $likeRepository,
        private DislikeRepositoryInterface $dislikeRepository,
        private RecommendationStrategyInterface $recommendationStrategy
    ) {}

    /**
     * Get recommended users for a given user.
     */
    public function getRecommendedUsers(User $user, array $filters, int $perPage): LengthAwarePaginator
    {
        // Get IDs of users already liked or disliked
        $likedUserIds = $this->likeRepository->getLikedUserIds($user->id);
        $dislikedUserIds = $this->dislikeRepository->getDislikedUserIds($user->id);

        $excludeIds = array_merge($likedUserIds, $dislikedUserIds);

        return $this->userRepository->getRecommendedUsers(
            $user->id,
            $excludeIds,
            $filters,
            $perPage
        );
    }

    /**
     * Get a user's profile with photos.
     */
    public function getUserProfile(int $userId): ?User
    {
        $user = $this->userRepository->find($userId);

        if ($user) {
            $user->load('photos');
        }

        return $user;
    }
}
