<?php

namespace App\Services;

use App\Models\Tool;
use App\Models\ToolConfigItem;
use App\Support\Psychology\PsychologyCatalog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class MoodCheckin
{
    public const CAPTIONS = [
        'very-happy' => 'Feeling great',
        'good' => 'Feeling positive',
        'neutral' => 'Neither good nor bad',
        'sad' => 'Feeling low',
        'stressed' => 'Feeling overwhelmed',
    ];

    public function isOpen(): bool
    {
        $tool = $this->tool();

        if (!$tool) {
            return true;
        }

        return !in_array($tool->status, ['inactive', 'unavailable'], true);
    }

    public function keys(): array
    {
        return PsychologyCatalog::moodKeys();
    }

    public function emotionKeys(): array
    {
        return PsychologyCatalog::emotionKeys();
    }

    public function emotionGroups(): array
    {
        $configured = $this->configuredEmotions();
        $groups = [
            'positive' => [],
            'neutral' => [],
            'difficult' => [],
        ];

        foreach (PsychologyCatalog::emotions() as $emotion) {
            $key = $emotion['item_key'];
            $bucket = $emotion['bucket'];
            if (!isset($groups[$bucket])) {
                continue;
            }

            $row = $configured->get($key);
            $groups[$bucket][] = [
                'key' => $key,
                'label' => filled($row?->label) ? $row->label : $emotion['label'],
                'bucket' => $bucket,
                'order' => $row?->display_order ?? $emotion['display_order'],
            ];
        }

        foreach ($groups as $bucket => $items) {
            usort($items, fn (array $a, array $b) => $a['order'] <=> $b['order']);
            $groups[$bucket] = $items;
        }

        return $groups;
    }

    public function summary(string $moodKey, array $emotionKeys): array
    {
        $moods = collect($this->options())->keyBy('key');
        $emotions = collect($this->emotionGroups())->flatten(1)->keyBy('key');
        $chips = [];
        foreach ($emotionKeys as $key) {
            if (!is_string($key) || !$emotions->has($key)) {
                continue;
            }
            $chips[] = [
                'key' => $key,
                'label' => $emotions[$key]['label'],
                'tone' => $emotions[$key]['bucket'] ?? $this->emotionTone($key),
            ];
        }

        return [
            'mood' => $moods->has($moodKey) ? $moods[$moodKey]['label'] : null,
            'emotions' => $chips,
        ];
    }

    private function emotionTone(string $key): string
    {
        foreach (PsychologyCatalog::emotions() as $emotion) {
            if ($emotion['item_key'] === $key) {
                return $emotion['bucket'] ?? 'neutral';
            }
        }

        return 'neutral';
    }

    public function options(): array
    {
        $configured = $this->configured();
        $options = [];

        foreach (PsychologyCatalog::moods() as $mood) {
            $key = $mood['item_key'];
            $row = $configured->get($key);
            $options[] = [
                'key' => $key,
                'label' => filled($row?->label) ? $row->label : $mood['label'],
                'caption' => self::CAPTIONS[$key],
                'tone' => in_array($key, ['sad', 'stressed'], true) ? 'difficult' : ($key === 'neutral' ? 'neutral' : 'positive'),
                'order' => $row?->display_order ?? $mood['display_order'],
            ];
        }

        usort($options, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $options;
    }

    private function tool(): ?Tool
    {
        try {
            if (!Schema::hasTable('tools')) {
                return null;
            }

            return Tool::query()->where('slug', 'daily-mood-check-in')->first();
        } catch (QueryException) {
            return null;
        }
    }

    private function configured()
    {
        try {
            $tool = $this->tool();
            if (!$tool || !Schema::hasTable('tool_config_items')) {
                return collect();
            }

            return $this->configRows('primary_mood', $this->keys());
        } catch (QueryException) {
            return collect();
        }
    }

    private function configuredEmotions()
    {
        try {
            $tool = $this->tool();
            if (!$tool || !Schema::hasTable('tool_config_items')) {
                return collect();
            }

            return $this->configRows('emotion', $this->emotionKeys());
        } catch (QueryException) {
            return collect();
        }
    }

    private function configRows(string $group, array $keys)
    {
        $tool = $this->tool();

        return ToolConfigItem::query()
            ->where('tool_id', $tool->id)
            ->where('group_key', $group)
            ->where('is_active', true)
            ->whereIn('item_key', $keys)
            ->get()
            ->keyBy('item_key');
    }
}
