<?php

namespace App\Strategies;

use App\Models\User;
use Illuminate\Support\Collection;

interface RecommendationStrategyInterface
{
    /**
     * Get recommended users based on the strategy.
     */
    public function getRecommendedUsers(User $user, array $excludeIds, array $filters, int $perPage): Collection;
}
