<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolConfigItem extends Model
{
    protected $fillable = [
        'tool_id',
        'group_key',
        'item_key',
        'label',
        'hint',
        'detail',
        'bucket',
        'is_active',
        'is_system',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'display_order' => 'integer',
    ];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class);
    }
}
