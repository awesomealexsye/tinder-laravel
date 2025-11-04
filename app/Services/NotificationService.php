<?php

namespace App\Services;

use App\Mail\UserPopularityNotification;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notify admin about a popular user.
     */
    public function notifyAdminAboutPopularUser(User $user, int $likeCount): void
    {
        $adminEmail = config('app.admin_email', 'admin@example.com');

        // Send email notification
        Mail::to($adminEmail)->send(new UserPopularityNotification($user, $likeCount));

        // Store notification record
        AdminNotification::create([
            'user_id' => $user->id,
            'like_count' => $likeCount,
            'email_sent_to' => $adminEmail,
            'email_sent_at' => now(),
        ]);
    }
}
