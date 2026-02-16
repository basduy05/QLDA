<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'locale',
        'messenger_terms_accepted_at',
        'terms_accepted_at',
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
            'last_seen_at' => 'datetime',
            'messenger_terms_accepted_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
        ];
    }

    public function isOnline(): bool
    {
        return Cache::has($this->onlineCacheKey());
    }

    public function activityStatusLabel(): string
    {
        if ($this->isOnline()) {
            return __('Active now');
        }

        if (! $this->last_seen_at) {
            return __('Inactive');
        }

        $minutes = (int) floor($this->last_seen_at->diffInSeconds(now()) / 60);

        if ($minutes < 1) {
            return __('Active now');
        }

        if ($minutes < 60) {
            return __('Inactive :value minute(s) ago', ['value' => $minutes]);
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return __('Inactive :value hour(s) ago', ['value' => $hours]);
        }

        $days = (int) floor($hours / 24);

        return __('Inactive :value day(s) ago', ['value' => $days]);
    }

    public function onlineCacheKey(): string
    {
        return 'presence:user:'.$this->id;
    }

    public function projectsOwned()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function unreadNotificationsCountSafe(): int
    {
        static $hasNotificationsTable;

        if ($hasNotificationsTable === null) {
            try {
                $hasNotificationsTable = Schema::hasTable('notifications');
            } catch (\Throwable) {
                $hasNotificationsTable = false;
            }
        }

        if (! $hasNotificationsTable) {
            return 0;
        }

        try {
            return $this->unreadNotifications()->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public function chatGroupsCreated()
    {
        return $this->hasMany(ChatGroup::class, 'created_by');
    }

    public function chatGroups()
    {
        return $this->belongsToMany(ChatGroup::class, 'chat_group_members')->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function directMessages()
    {
        return $this->hasMany(DirectMessage::class);
    }
}
