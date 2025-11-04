<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PhotoRepositoryInterface
{
    /**
     * Create multiple photos for a user.
     */
    public function createMany(int $userId, array $photos): Collection;

    /**
     * Get photos for a specific user.
     */
    public function getByUserId(int $userId): Collection;
}
