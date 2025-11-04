<?php

namespace App\Repositories;

use App\Models\Photo;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use Illuminate\Support\Collection;

class PhotoRepository implements PhotoRepositoryInterface
{
    /**
     * Create multiple photos for a user.
     */
    public function createMany(int $userId, array $photos): Collection
    {
        $photoRecords = [];

        foreach ($photos as $index => $photoUrl) {
            $photoRecords[] = Photo::create([
                'user_id' => $userId,
                'url' => $photoUrl,
                'display_order' => $index + 1,
                'is_primary' => $index === 0,
            ]);
        }

        return collect($photoRecords);
    }

    /**
     * Get photos for a specific user.
     */
    public function getByUserId(int $userId): Collection
    {
        return Photo::where('user_id', $userId)
            ->orderBy('display_order')
            ->get();
    }
}
