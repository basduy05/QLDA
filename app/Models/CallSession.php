<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallSession extends Model
{
    protected $fillable = [
        'caller_id',
        'callee_id',
        'status',
        'offer_sdp',
        'answer_sdp',
        'accepted_at',
        'ended_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function callee()
    {
        return $this->belongsTo(User::class, 'callee_id');
    }

    public function involves(int $userId): bool
    {
        return $this->caller_id === $userId || $this->callee_id === $userId;
    }
}
