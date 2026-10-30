<?php

namespace Tests\Feature;

use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\ToolConfigItem;
use App\Services\MoodCheckinDraft;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MoodCheckinTest extends TestCase
{
    public function test_mood_screen_lists_five_options_and_keeps_continue_disabled(): void
    {
        $this->get(route('tools.psychology.mood'))
            ->assertOk()
            ->assertSee('Free tool')
            ->assertSee('How are you feeling today?')
            ->assertSee('Choose the option that best describes how you feel right now.')
            ->assertSee('Very happy')
            ->assertSee('Feeling great')
            ->assertSee('Good')
            ->assertSee('Feeling positive')
            ->assertSee('Neutral')
            ->assertSee('Neither good nor bad')
            ->assertSee('Sad')
            ->assertSee('Feeling low')
            ->assertSee('Stressed')
            ->assertSee('Feeling overwhelmed')
            ->assertSee('Continue')
            ->assertSee('href="'.route('tools.psychology').'"', false)
            ->assertSee('<button class="yk-mood__continue" type="submit" data-mood-continue disabled>Continue</button>', false)
            ->assertSee('family=DM+Sans', false)
            ->assertSee('yuktione-mood.css', false)
            ->assertSee('Yuktione_logo.png', false)
            ->assertDontSee('data-selected="true"', false);

        $this->get(route('tools.psychology'))
            ->assertOk()
            ->assertSee(route('tools.psychology.mood'), false)
            ->assertSee('Coming soon');
    }

    public function test_one_mood_is_stored_as_a_draft_and_replaces_the_previous_choice(): void
    {
        $this->post(route('tools.psychology.mood.store'), ['mood' => 'Feeling great'])
            ->assertSessionHasErrors('mood');
        $this->assertNull(session(MoodCheckinDraft::KEY));

        $this->post(route('tools.psychology.mood.store'), ['mood' => ['good', 'sad']])
            ->assertSessionHasErrors('mood');

        $this->post(route('tools.psychology.mood.store'), ['mood' => 'good'])
            ->assertRedirect(route('tools.psychology.mood.emotions'));

        $this->get(route('tools.psychology.mood'))
            ->assertOk()
            ->assertSee('value="good"', false)
            ->assertSee('data-selected="true"', false);

        $this->assertSame(1, substr_count($this->get(route('tools.psychology.mood'))->getContent(), 'data-selected="true"'));

        $this->post(route('tools.psychology.mood.store'), ['mood' => 'sad'])
            ->assertRedirect(route('tools.psychology.mood.emotions'));

        $response = $this->get(route('tools.psychology.mood'));
        $response->assertSee('value="sad"', false);
        $this->assertSame(1, substr_count($response->getContent(), 'data-selected="true"'));
        $this->assertSame('sad', session(MoodCheckinDraft::KEY)['mood']);
        $this->assertSame('mood', session(MoodCheckinDraft::KEY)['step']);
        $this->assertArrayNotHasKey('label', session(MoodCheckinDraft::KEY));
    }

    public function test_a_draft_belongs_only_to_its_owner_and_expires(): void
    {
        $this->post(route('tools.psychology.mood.store'), ['mood' => 'stressed']);

        $this->withSession([
            MoodCheckinDraft::KEY => [
                'step' => 'mood',
                'mood' => 'stressed',
                'owner' => 'user:42',
                'expires_at' => now()->addMinutes(10)->getTimestamp(),
            ],
        ])->get(route('tools.psychology.mood'))
            ->assertOk()
            ->assertDontSee('data-selected="true"', false);

        $this->withSession([
            'psychology.mood_guest' => 'guest-token',
            MoodCheckinDraft::KEY => [
                'step' => 'mood',
                'mood' => 'stressed',
                'owner' => 'guest:guest-token',
                'expires_at' => now()->subMinute()->getTimestamp(),
            ],
        ])->get(route('tools.psychology.mood'))
            ->assertDontSee('data-selected="true"', false);
    }

    public function test_admin_wording_is_used_and_a_disabled_tool_stays_closed(): void
    {
        $this->registry();
        $tool = Tool::query()->where('slug', 'daily-mood-check-in')->first();
        ToolConfigItem::query()->create([
            'tool_id' => $tool->id,
            'group_key' => 'primary_mood',
            'item_key' => 'very-happy',
            'label' => 'Really glad',
            'is_active' => true,
            'is_system' => true,
            'display_order' => 1,
        ]);

        $this->get(route('tools.psychology.mood'))
            ->assertOk()
            ->assertSee('Really glad')
            ->assertSee('Feeling great')
            ->assertDontSee('>Very happy<', false);

        $this->post(route('tools.psychology.mood.store'), ['mood' => 'very-happy'])
            ->assertRedirect(route('tools.psychology.mood.emotions'));
        $this->assertSame('very-happy', session(MoodCheckinDraft::KEY)['mood']);

        $tool->update(['status' => 'unavailable']);

        $this->get(route('tools.psychology.mood'))
            ->assertOk()
            ->assertSee('unavailable right now')
            ->assertDontSee('Feeling great');

        $this->post(route('tools.psychology.mood.store'), ['mood' => 'good'])
            ->assertForbidden();
    }

    private function registry(): void
    {
        Schema::create('tool_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_category_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('module_key');
            $table->string('status')->default('coming_soon');
            $table->unsignedInteger('display_order')->default(0);
            $table->string('route_name')->nullable();
            $table->boolean('is_implemented')->default(false);
            $table->timestamps();
        });
        Schema::create('tool_config_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->string('group_key');
            $table->string('item_key');
            $table->string('label');
            $table->text('hint')->nullable();
            $table->text('detail')->nullable();
            $table->string('bucket')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        $category = ToolCategory::query()->create([
            'name' => 'Psychology',
            'slug' => 'psychology',
            'display_order' => 1,
        ]);
        Tool::query()->create([
            'tool_category_id' => $category->id,
            'name' => 'Daily Mood Check-in',
            'slug' => 'daily-mood-check-in',
            'module_key' => 'psychology',
            'status' => 'coming_soon',
            'is_implemented' => false,
            'display_order' => 1,
        ]);
    }
}
