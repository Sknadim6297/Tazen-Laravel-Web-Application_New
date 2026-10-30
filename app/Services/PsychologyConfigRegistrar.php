<?php

namespace App\Services;

use App\Models\Tool;
use App\Models\ToolConfigItem;
use App\Support\Psychology\PsychologyCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PsychologyConfigRegistrar
{
    public function sync(): void
    {
        if (!Schema::hasTable('tools') || !Schema::hasTable('tool_config_items')) {
            return;
        }

        app(ToolRegistryRegistrar::class)->register();

        DB::transaction(function () {
            foreach (PsychologyCatalog::definitions() as $slug => $items) {
                $tool = Tool::query()->where('slug', $slug)->first();
                if (!$tool) {
                    continue;
                }

                foreach ($items as $item) {
                    ToolConfigItem::query()->firstOrCreate(
                        [
                            'tool_id' => $tool->id,
                            'group_key' => $item['group_key'],
                            'item_key' => $item['item_key'],
                        ],
                        [
                            'label' => $item['label'],
                            'hint' => $item['hint'],
                            'detail' => $item['detail'],
                            'bucket' => $item['bucket'],
                            'is_active' => $item['is_active'],
                            'is_system' => $item['is_system'],
                            'display_order' => $item['display_order'],
                        ]
                    );
                }
            }
        });
    }
}
