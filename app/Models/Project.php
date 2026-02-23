<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public const ROLE_LEAD = 'lead';
    public const ROLE_DEPUTY = 'deputy';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public static function roles(): array
    {
        return [self::ROLE_LEAD, self::ROLE_DEPUTY, self::ROLE_MEMBER];
    }

    public function roleForUser(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ((int) $this->owner_id === (int) $user->id) {
            return self::ROLE_LEAD;
        }

        $member = $this->members->firstWhere('id', $user->id);

        return $member?->pivot?->role;
    }

    public function userHasRole(?User $user, array $roles): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $role = $this->roleForUser($user);

        return $role !== null && in_array($role, $roles, true);
    }
}
