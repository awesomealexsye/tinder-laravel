<?php

namespace Database\Seeders;

use App\Models\Dislike;
use App\Models\Like;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Creating test user...');

        // Create test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 28,
        ]);

        // Create photos for test user
        Photo::factory()->count(3)->create([
            'user_id' => $testUser->id,
            'is_primary' => false,
        ]);

        Photo::factory()->primary()->create([
            'user_id' => $testUser->id,
        ]);

        $this->command->info('Creating 100 users with photos...');

        // Create 100 random users
        $users = User::factory()->count(100)->create();

        // Create 3-5 photos for each user
        foreach ($users as $user) {
            $photoCount = rand(3, 5);

            // Create primary photo
            Photo::factory()->primary()->create([
                'user_id' => $user->id,
            ]);

            // Create additional photos
            for ($i = 2; $i <= $photoCount; $i++) {
                Photo::factory()->create([
                    'user_id' => $user->id,
                    'display_order' => $i,
                ]);
            }
        }

        $this->command->info('Creating random likes...');

        // Create random likes
        $allUsers = User::all();
        $likeCount = 0;

        foreach ($allUsers as $user) {
            $otherUsers = $allUsers->where('id', '!=', $user->id)->random(rand(0, 20));

            foreach ($otherUsers as $likedUser) {
                try {
                    Like::create([
                        'liker_id' => $user->id,
                        'liked_id' => $likedUser->id,
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]);
                    $likeCount++;
                } catch (\Exception $e) {
                    // Skip duplicate likes
                }
            }
        }

        $this->command->info("Created {$likeCount} likes");

        // Ensure 2-3 users have 50+ likes for testing
        $this->command->info('Creating popular users with 50+ likes...');

        $popularUsers = $allUsers->random(3);
        foreach ($popularUsers as $popularUser) {
            $likers = $allUsers->where('id', '!=', $popularUser->id)->random(min(55, $allUsers->count() - 1));

            foreach ($likers as $liker) {
                try {
                    Like::firstOrCreate([
                        'liker_id' => $liker->id,
                        'liked_id' => $popularUser->id,
                    ], [
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]);
                } catch (\Exception $e) {
                    // Skip duplicates
                }
            }

            $count = Like::where('liked_id', $popularUser->id)->count();
            $this->command->info("  User '{$popularUser->name}' now has {$count} likes");
        }

        $this->command->info('Creating random dislikes...');

        // Create random dislikes
        $dislikeCount = 0;
        foreach ($allUsers->random(30) as $user) {
            $otherUsers = $allUsers->where('id', '!=', $user->id)->random(rand(0, 10));

            foreach ($otherUsers as $dislikedUser) {
                try {
                    Dislike::create([
                        'user_id' => $user->id,
                        'disliked_id' => $dislikedUser->id,
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]);
                    $dislikeCount++;
                } catch (\Exception $e) {
                    // Skip duplicates
                }
            }
        }

        $this->command->info("Created {$dislikeCount} dislikes");
        $this->command->info('Database seeding completed!');
    }
}
