<?php

namespace Tests\Feature;

use App\Models\PsychologyMoodCheckin;
use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class MoodHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckActiveSession::class,
            \App\Http\Middleware\TrackUserSession::class,
        ]);
    }

    public function test_history_lists_only_the_signed_in_customers_check_ins(): void
    {
        $this->storage();
        $member = $this->member('history@example.com');
        $other = $this->member('other-history@example.com');

        $this->get(route('tools.psychology.mood.history'))
            ->assertRedirect(route('login'));
        $this->assertSame(route('tools.psychology.mood.history'), session('url.intended'));

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history'))
            ->assertOk()
            ->assertSee('Mood history')
            ->assertSee('No check-ins yet')
            ->assertSee('Start a check-in')
            ->assertDontSee('Older');

        $this->checkin($member, 'good', ['calm', 'grateful'], '<script>alert(1)</script>', '2026-10-30 18:05:00');
        $this->checkin($member, 'sad', [], null, '2026-10-29 18:05:00');
        $this->checkin($other, 'stressed', ['worried'], 'Someone else wrote this', '2026-10-30 19:00:00');

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history'))
            ->assertOk()
            ->assertSee('Good')
            ->assertSee('Calm')
            ->assertSee('Grateful')
            ->assertSee('30 Oct 2026, 11:35 PM')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Sad')
            ->assertSee('No emotions selected.')
            ->assertSee('No reflection added.')
            ->assertDontSee('Someone else wrote this')
            ->assertDontSee('Stressed');

        $this->actingAs($other, 'user')
            ->get(route('tools.psychology.mood.history'))
            ->assertOk()
            ->assertSee('Someone else wrote this')
            ->assertDontSee('&lt;script&gt;', false);

        $this->assertSame(3, PsychologyMoodCheckin::query()->count());
        $this->actingAs($member, 'user')->get(route('tools.psychology.mood.history'));
        $this->assertSame(3, PsychologyMoodCheckin::query()->count());
    }

    public function test_history_pages_newest_first_and_rejects_an_unsafe_page(): void
    {
        $this->storage();
        $member = $this->member('pages@example.com');
        $other = $this->member('pages-other@example.com');
        $this->checkin($other, 'stressed', ['worried'], 'Hidden on every page', '2026-10-30 20:00:00');

        foreach (range(1, 9) as $day) {
            $this->checkin(
                $member,
                'neutral',
                ['quiet'],
                'Entry '.$day,
                sprintf('2026-10-%02d 12:00:00', $day)
            );
        }

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history'))
            ->assertOk()
            ->assertSee('Entry 9')
            ->assertSee('Entry 2')
            ->assertDontSee('Entry 1')
            ->assertSee('Page 1 of 2')
            ->assertSee('Older')
            ->assertDontSee('Hidden on every page');

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history', ['page' => 2]))
            ->assertOk()
            ->assertSee('Entry 1')
            ->assertDontSee('Entry 2')
            ->assertSee('Newer')
            ->assertSee('Page 2 of 2')
            ->assertDontSee('Hidden on every page');

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history', ['page' => 9]))
            ->assertRedirect(route('tools.psychology.mood.history', ['page' => 2]));

        foreach (['0', '-1', '1.5', '999999', '1abc'] as $page) {
            $this->actingAs($member, 'user')
                ->get(route('tools.psychology.mood.history', ['page' => $page]))
                ->assertRedirect(route('tools.psychology.mood.history'));
        }

        $this->actingAs($member, 'user')
            ->get(route('tools.psychology.mood.history').'?page[]=2')
            ->assertRedirect(route('tools.psychology.mood.history'));
    }

    public function test_a_saved_check_in_appears_in_history_when_the_tool_stays_unpublished(): void
    {
        $this->storage();
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
            $table->string('status');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_implemented')->default(false);
            $table->timestamps();
        });
        $category = ToolCategory::query()->create(['name' => 'Psychology', 'slug' => 'psychology']);
        Tool::query()->create([
            'tool_category_id' => $category->id,
            'name' => 'Daily Mood Check-in',
            'slug' => 'daily-mood-check-in',
            'module_key' => 'psychology',
            'status' => 'coming_soon',
            'is_implemented' => false,
        ]);

        $member = $this->member('saved-history@example.com');
        $this->actingAs($member, 'user');
        $this->post(route('tools.psychology.mood.store'), ['mood' => 'good']);
        $this->post(route('tools.psychology.mood.emotions.store'), ['emotions' => ['calm']]);
        $this->post(route('tools.psychology.mood.reflection.store'), [
            'action' => 'save',
            'reflection' => 'Kept for history',
        ])->assertRedirect();

        $this->get(route('tools.psychology.mood.history'))
            ->assertOk()
            ->assertSee('Good')
            ->assertSee('Calm')
            ->assertSee('Kept for history');
        $this->assertSame(1, PsychologyMoodCheckin::query()->count());
        $this->assertFalse((bool) Tool::query()->where('slug', 'daily-mood-check-in')->value('is_implemented'));
        $this->assertSame('coming_soon', Tool::query()->where('slug', 'daily-mood-check-in')->value('status'));
    }

    private function storage(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('profile_image')->nullable();
        });
        Schema::create('psychology_mood_checkins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->uuid('completion_key')->unique();
            $table->string('mood_key');
            $table->json('emotion_keys');
            $table->text('reflection')->nullable();
            $table->timestamp('checked_in_at');
            $table->timestamps();
        });
    }

    private function member(string $email): User
    {
        return User::query()->create([
            'name' => 'Mood Member',
            'email' => $email,
            'password' => 'password-123',
        ]);
    }

    private function checkin(User $member, string $mood, array $emotions, ?string $reflection, string $at): void
    {
        $record = new PsychologyMoodCheckin([
            'completion_key' => (string) Str::uuid(),
            'mood_key' => $mood,
            'emotion_keys' => $emotions,
            'reflection' => $reflection,
            'checked_in_at' => Carbon::parse($at, 'UTC'),
        ]);
        $record->user_id = $member->id;
        $record->save();
    }
}
