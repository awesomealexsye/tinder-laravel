<?php

namespace App\Strategies;

use App\Models\User;
use Illuminate\Support\Collection;

class RandomRecommendationStrategy implements RecommendationStrategyInterface
{
    /**
     * Get recommended users using random selection.
     */
    public function getRecommendedUsers(User $user, array $excludeIds, array $filters, int $perPage): Collection
    {
        $query = User::query()
            ->with('photos')
            ->where('id', '!=', $user->id)
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

        return $query->inRandomOrder()->limit($perPage)->get();
    }
}
