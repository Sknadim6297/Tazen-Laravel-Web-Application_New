<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychologyMoodCheckin extends Model
{
    protected $fillable = [
        'completion_key',
        'mood_key',
        'emotion_keys',
        'reflection',
        'checked_in_at',
    ];

    protected $casts = [
        'emotion_keys' => 'array',
        'checked_in_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
