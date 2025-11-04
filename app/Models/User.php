<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'gender',
        'bio',
        'location',
        'latitude',
        'longitude',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the photos for the user.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Get the likes given by this user.
     */
    public function likesGiven(): HasMany
    {
        return $this->hasMany(Like::class, 'liker_id');
    }

    /**
     * Get the likes received by this user.
     */
    public function likesReceived(): HasMany
    {
        return $this->hasMany(Like::class, 'liked_id');
    }

    /**
     * Get the dislikes given by this user.
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(Dislike::class, 'user_id');
    }

    /**
     * Get the admin notifications for this user.
     */
    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }
}
