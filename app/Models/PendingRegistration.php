<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'terms_accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'terms_accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
