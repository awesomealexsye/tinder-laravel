<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'like_count',
        'email_sent_to',
        'email_sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'like_count' => 'integer',
            'email_sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user that this notification is about.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
