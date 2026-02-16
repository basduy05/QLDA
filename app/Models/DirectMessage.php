<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectMessage extends Model
{
    protected $fillable = [
        'direct_conversation_id',
        'user_id',
        'body',
    ];

    public function conversation()
    {
        return $this->belongsTo(DirectConversation::class, 'direct_conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
