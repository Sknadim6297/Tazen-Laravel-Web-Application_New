<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesToolRegistry;
use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolConfigItem;
use App\Services\PsychologyConfigRegistrar;
use App\Services\ToolConfigStore;
use App\Support\Psychology\PsychologyCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PsychologySettingController extends Controller
{
    use AuthorizesToolRegistry;

    public function edit(Request $request, PsychologyConfigRegistrar $registrar, ToolConfigStore $store)
    {
        $this->authorizeToolRegistry('tools.psychology');
        $this->prepare($registrar);

        $tab = $this->tab($request->query('tab'));
        $tools = $this->tools();

        return view('admin.tools.psychology.settings', [
            'tab' => $tab,
            'tools' => $tools,
            'moods' => $store->items($tools['daily-mood-check-in'])->where('group_key', 'primary_mood'),
            'emotions' => $store->items($tools['daily-mood-check-in'])->where('group_key', 'emotion'),
            'prompts' => $store->items($tools['digital-journal'])->where('group_key', 'journal_prompt'),
            'cards' => $store->items($tools['gratitude-journal'])->where('group_key', 'gratitude_card'),
            'notes' => PsychologyCatalog::NOTES,
            'buckets' => [
                'positive' => 'Positive',
                'neutral' => 'Neutral',
                'difficult' => 'Difficult',
            ],
        ]);
    }

    public function update(Request $request, PsychologyConfigRegistrar $registrar, ToolConfigStore $store)
    {
        $this->authorizeToolRegistry('tools.psychology');
        $this->prepare($registrar);

        $data = $request->validate([
            'tab' => ['nullable', Rule::in(['mood', 'journal', 'gratitude'])],
            'moods' => ['required', 'array', 'size:5'],
            'moods.*.label' => ['required', 'string', 'max:60'],
            'moods.*.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'moods.*.is_active' => ['prohibited'],
            'emotions' => ['required', 'array', 'size:15'],
            'emotions.*.label' => ['required', 'string', 'max:60'],
            'emotions.*.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'emotions.*.is_active' => ['prohibited'],
            'emotions.*.bucket' => ['prohibited'],
            'prompts' => ['required', 'array', 'min:6'],
            'prompts.*.label' => ['required', 'string', 'max:120'],
            'prompts.*.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'prompts.*.is_active' => ['nullable', 'boolean'],
            'new_prompt.label' => ['nullable', 'string', 'max:120'],
            'new_prompt.display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'remove_prompts' => ['nullable', 'array'],
            'remove_prompts.*' => ['integer'],
            'cards' => ['required', 'array', 'size:3'],
            'cards.*.label' => ['required', 'string', 'max:80'],
            'cards.*.hint' => ['required', 'string', 'max:400'],
            'cards.*.detail' => ['required', 'string', 'max:400'],
            'cards.*.is_active' => ['prohibited'],
            'reflection_max' => ['prohibited'],
            'max_emotions' => ['prohibited'],
            'primary_mood_count' => ['prohibited'],
            'status' => ['prohibited'],
            'route_name' => ['prohibited'],
            'is_implemented' => ['prohibited'],
        ]);

        $this->assertKeys($data['moods'], PsychologyCatalog::moodKeys(), 'moods', 'The five primary moods must stay in place.');
        $this->assertKeys($data['emotions'], PsychologyCatalog::emotionKeys(), 'emotions', 'The emotion list must stay grouped as documented.');
        $this->assertPromptKeys(array_keys($data['prompts']));
        $this->assertKeys($data['cards'], PsychologyCatalog::cardKeys(), 'cards', 'Gratitude stays a three-card experience.');

        foreach ($data['prompts'] as $key => $row) {
            $data['prompts'][$key]['is_active'] = $request->boolean('prompts.'.$key.'.is_active');
        }

        $store->savePsychology(
            $data['moods'],
            $data['emotions'],
            $data['prompts'],
            $data['new_prompt'] ?? null,
            $data['remove_prompts'] ?? [],
            $data['cards']
        );

        return redirect()
            ->route('admin.psychology.settings.edit', ['tab' => $this->tab($data['tab'] ?? null)])
            ->with('success', 'Psychology settings saved. Tool availability was left unchanged.');
    }

    private function prepare(PsychologyConfigRegistrar $registrar): void
    {
        if (!Schema::hasTable('tool_config_items') || !Schema::hasTable('tools')) {
            abort(503, 'Psychology settings are not installed yet.');
        }

        $registrar->sync();
    }

    private function tools()
    {
        return Tool::query()
            ->whereIn('slug', array_keys(PsychologyCatalog::definitions()))
            ->get()
            ->keyBy('slug');
    }

    private function tab(?string $tab): string
    {
        return in_array($tab, ['mood', 'journal', 'gratitude'], true) ? $tab : 'mood';
    }

    private function assertKeys(array $rows, array $expected, string $field, string $message): void
    {
        $actual = array_keys($rows);
        sort($actual);
        $expected = array_values($expected);
        sort($expected);

        if ($actual !== $expected) {
            throw ValidationException::withMessages([$field => $message]);
        }
    }

    private function assertPromptKeys(array $keys): void
    {
        $journal = Tool::query()->where('slug', 'digital-journal')->firstOrFail();
        $known = ToolConfigItem::query()
            ->where('tool_id', $journal->id)
            ->where('group_key', 'journal_prompt')
            ->pluck('item_key')
            ->all();

        $missing = array_diff(PsychologyCatalog::promptKeys(), $keys);
        $unknown = array_diff($keys, $known);

        if ($missing !== [] || $unknown !== []) {
            throw ValidationException::withMessages([
                'prompts' => 'Documented journal prompts must remain. New prompts are added separately.',
            ]);
        }
    }
}
