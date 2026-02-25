<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'user_message',
        'ai_response',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
