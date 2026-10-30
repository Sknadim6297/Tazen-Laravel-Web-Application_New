<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;

class Tool extends Model
{
    public const STATUSES = ['coming_soon', 'unavailable', 'active', 'inactive'];

    public const ACCESS_TYPES = ['free', 'paid'];

    protected $fillable = [
        'tool_category_id',
        'name',
        'slug',
        'module_key',
        'description',
        'icon',
        'access_type',
        'status',
        'display_order',
        'route_name',
        'is_implemented',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_implemented' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ToolCategory::class, 'tool_category_id');
    }

    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_implemented', true)
            ->where('status', 'active')
            ->whereNotNull('route_name');
    }

    public function canLaunch(): bool
    {
        return $this->is_implemented
            && $this->status === 'active'
            && filled($this->route_name)
            && Route::has($this->route_name);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'coming_soon' => 'Coming Soon',
            'unavailable' => 'Unavailable',
            'active' => 'Active',
            'inactive' => 'Inactive',
            default => ucfirst((string) $this->status),
        };
    }
}
