<?php

namespace App\Services;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Support\Facades\DB;

class ToolRegistryRegistrar
{
    public function register(): void
    {
        DB::transaction(function () {
            $category = ToolCategory::query()->firstOrCreate(
                ['slug' => 'psychology'],
                [
                    'name' => 'Psychology',
                    'description' => 'Private wellbeing tools. User entries stay outside this registry.',
                    'icon' => 'ri-mental-health-line',
                    'is_active' => true,
                    'display_order' => 1,
                ]
            );

            $tools = [
                [
                    'slug' => 'daily-mood-check-in',
                    'name' => 'Daily Mood Check-in',
                    'description' => 'Understand and track your emotions.',
                    'icon' => 'ri-emotion-line',
                    'display_order' => 1,
                    'status' => 'active',
                    'route_name' => 'tools.psychology.mood',
                    'is_implemented' => true,
                ],
                [
                    'slug' => 'digital-journal',
                    'name' => 'Digital Journal',
                    'description' => 'A private journal. The workflow is not built yet.',
                    'icon' => 'ri-book-open-line',
                    'display_order' => 2,
                ],
                [
                    'slug' => 'gratitude-journal',
                    'name' => 'Gratitude Journal',
                    'description' => 'A private gratitude record. The workflow is not built yet.',
                    'icon' => 'ri-heart-2-line',
                    'display_order' => 3,
                ],
            ];

            foreach ($tools as $tool) {
                Tool::query()->firstOrCreate(
                    ['slug' => $tool['slug']],
                    [
                        'tool_category_id' => $category->id,
                        'name' => $tool['name'],
                        'module_key' => 'psychology',
                        'description' => $tool['description'],
                        'icon' => $tool['icon'],
                        'access_type' => 'free',
                        'status' => $tool['status'] ?? 'coming_soon',
                        'display_order' => $tool['display_order'],
                        'route_name' => $tool['route_name'] ?? null,
                        'is_implemented' => $tool['is_implemented'] ?? false,
                    ]
                );
            }
        });
    }
}
