<?php

namespace App\Console\Commands;

use App\Events\UserReached50LikesEvent;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

class CheckPopularUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-popular-users {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for users with 50+ likes and send admin notifications';

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for popular users with 50+ likes...');

        $popularUsers = $this->userRepository->getUsersWithLikeCountAbove(50);
        $count = 0;

        foreach ($popularUsers as $user) {
            $likeCount = $user->likes_received_count;

            $this->info("Found user: {$user->name} (ID: {$user->id}) with {$likeCount} likes");

            if ($this->option('dry-run')) {
                $this->warn('  [DRY RUN] Skipping notification');
            } else {
                event(new UserReached50LikesEvent($user, $likeCount));
                $this->info('  Notification sent');
            }

            $count++;
        }

        if ($count === 0) {
            $this->info('No users found with 50+ likes who haven\'t been notified');
        } else {
            $this->info("Processed {$count} popular user(s)");
        }

        return Command::SUCCESS;
    }
}
