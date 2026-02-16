<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    protected $fillable = [
        'email',
        'purpose',
        'code_hash',
        'attempts',
        'max_attempts',
        'expires_at',
        'used_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
