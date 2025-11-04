<?php

namespace App\Listeners;

use App\Events\UserReached50LikesEvent;
use App\Services\NotificationService;

class SendAdminNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(UserReached50LikesEvent $event): void
    {
        $this->notificationService->notifyAdminAboutPopularUser(
            $event->user,
            $event->likeCount
        );
    }
}
