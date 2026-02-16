<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
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
}
