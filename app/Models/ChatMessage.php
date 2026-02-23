<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_group_id',
        'user_id',
        'body',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'attachment_mime',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'attachment_size' => 'integer',
        ];
    }

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
