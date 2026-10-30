<?php

namespace App\Services;

use App\Models\Tool;
use App\Models\ToolConfigItem;
use App\Support\Psychology\PsychologyCatalog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ToolConfigStore
{
    public function items(Tool $tool): Collection
    {
        return Cache::remember($this->cacheKey($tool), 3600, function () use ($tool) {
            return $tool->configItems()->orderBy('display_order')->orderBy('id')->get();
        });
    }

    public function forget(Tool $tool): void
    {
        Cache::forget($this->cacheKey($tool));
    }

    public function savePsychology(array $moods, array $emotions, array $prompts, ?array $newPrompt, array $removeIds, array $cards): void
    {
        $tools = Tool::query()
            ->whereIn('slug', array_keys(PsychologyCatalog::definitions()))
            ->get()
            ->keyBy('slug');

        DB::transaction(function () use ($tools, $moods, $emotions, $prompts, $newPrompt, $removeIds, $cards) {
            $this->updateFixed($tools['daily-mood-check-in'], 'primary_mood', $moods, ['label', 'display_order']);
            $this->updateFixed($tools['daily-mood-check-in'], 'emotion', $emotions, ['label', 'display_order']);
            $this->updatePrompts($tools['digital-journal'], $prompts, $removeIds);
            $this->addPrompt($tools['digital-journal'], $newPrompt);
            $this->updateCards($tools['gratitude-journal'], $cards);
        });

        $tools->each(fn (Tool $tool) => $this->forget($tool));
    }

    private function updateFixed(Tool $tool, string $group, array $rows, array $fields): void
    {
        foreach ($rows as $key => $row) {
            $item = $this->findItem($tool, $group, $key);
            $changes = ['is_active' => true];
            foreach ($fields as $field) {
                $changes[$field] = $field === 'display_order'
                    ? (int) $row[$field]
                    : $this->requiredText($row[$field] ?? '', $field === 'label' ? 80 : 400, $group);
            }
            $item->update($changes);
        }
    }

    private function updatePrompts(Tool $tool, array $rows, array $removeIds): void
    {
        $removeIds = array_map('intval', $removeIds);

        foreach ($rows as $key => $row) {
            $item = $this->findItem($tool, 'journal_prompt', $key);
            if (in_array($item->id, $removeIds, true)) {
                continue;
            }
            $item->update([
                'label' => $this->requiredText($row['label'] ?? '', 120, 'prompts'),
                'display_order' => (int) $row['display_order'],
                'is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        if ($removeIds === []) {
            return;
        }

        $items = ToolConfigItem::query()
            ->where('tool_id', $tool->id)
            ->where('group_key', 'journal_prompt')
            ->whereIn('id', $removeIds)
            ->get();

        if ($items->count() !== count(array_unique($removeIds))) {
            throw ValidationException::withMessages([
                'remove_prompts' => 'Only journal prompts from this tool can be removed.',
            ]);
        }

        foreach ($items as $item) {
            if ($item->is_system || $this->referencedByUserData($item)) {
                throw ValidationException::withMessages([
                    'remove_prompts' => 'Documented prompts stay in the catalogue. Turn one off instead of deleting it.',
                ]);
            }
            $item->delete();
        }
    }

    private function addPrompt(Tool $tool, ?array $newPrompt): void
    {
        $label = $this->clean($newPrompt['label'] ?? '', 120);
        if ($label === '') {
            return;
        }

        $base = Str::slug($label);
        if ($base === '') {
            throw ValidationException::withMessages([
                'new_prompt.label' => 'Enter a prompt name with letters or numbers.',
            ]);
        }

        $key = $base;
        $suffix = 2;
        while ($this->promptKeyExists($tool, $key)) {
            $key = $base.'-'.$suffix;
            $suffix++;
            if ($suffix > 50) {
                throw ValidationException::withMessages([
                    'new_prompt.label' => 'That prompt could not be given a unique key.',
                ]);
            }
        }

        ToolConfigItem::query()->create([
            'tool_id' => $tool->id,
            'group_key' => 'journal_prompt',
            'item_key' => $key,
            'label' => $label,
            'is_active' => true,
            'is_system' => false,
            'display_order' => (int) ($newPrompt['display_order'] ?? 100),
        ]);
    }

    private function updateCards(Tool $tool, array $rows): void
    {
        foreach ($rows as $key => $row) {
            $item = $this->findItem($tool, 'gratitude_card', $key);
            $item->update([
                'label' => $this->requiredText($row['label'] ?? '', 80, 'cards'),
                'hint' => $this->requiredText($row['hint'] ?? '', 400, 'cards', true),
                'detail' => $this->requiredText($row['detail'] ?? '', 400, 'cards', true),
                'is_active' => true,
                'display_order' => $item->display_order,
            ]);
        }
    }

    private function findItem(Tool $tool, string $group, string $key): ToolConfigItem
    {
        $item = ToolConfigItem::query()
            ->where('tool_id', $tool->id)
            ->where('group_key', $group)
            ->where('item_key', $key)
            ->first();

        if (!$item) {
            throw ValidationException::withMessages([
                $group => 'Unknown configuration key: '.$key,
            ]);
        }

        return $item;
    }

    private function promptKeyExists(Tool $tool, string $key): bool
    {
        return ToolConfigItem::query()
            ->where('tool_id', $tool->id)
            ->where('group_key', 'journal_prompt')
            ->where('item_key', $key)
            ->exists();
    }

    private function referencedByUserData(ToolConfigItem $item): bool
    {
        foreach (['mood_checkins', 'journal_entries', 'gratitude_entries'] as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'config_item_id')) {
                continue;
            }
            if (DB::table($table)->where('config_item_id', $item->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    private function requiredText(string $value, int $max, string $field, bool $keepLines = false): string
    {
        $text = $this->clean($value, $max, $keepLines);
        if ($text === '') {
            throw ValidationException::withMessages([
                $field => 'Display text cannot be empty.',
            ]);
        }

        return $text;
    }

    private function clean(string $value, int $max, bool $keepLines = false): string
    {
        $text = strip_tags($value);
        if (!$keepLines) {
            $text = preg_replace('/\s+/', ' ', $text) ?? '';
        }

        return mb_substr(trim($text), 0, $max);
    }

    private function cacheKey(Tool $tool): string
    {
        return 'tool-config.items.'.$tool->id;
    }
}
