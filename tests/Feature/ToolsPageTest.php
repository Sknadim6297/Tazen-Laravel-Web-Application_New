<?php

namespace Tests\Feature;

use Tests\TestCase;

class ToolsPageTest extends TestCase
{
    public function test_tools_page_uses_the_real_empty_catalogue(): void
    {
        $this->get(route('tools.index'))
            ->assertOk()
            ->assertSee('Tools for a')
            ->assertSee('brighter you.')
            ->assertSee('Showing 0 tools')
            ->assertSee('does not have a public tools catalogue yet')
            ->assertSee('family=DM+Sans', false)
            ->assertSee('yuktione-tools-page.css', false)
            ->assertDontSee('Guided Meditation')
            ->assertDontSee('Anxiety Relief Toolkit')
            ->assertDontSee('Open Tool');
    }

    public function test_tools_search_and_category_stay_honest(): void
    {
        $this->get(route('tools.index', ['q' => 'Sleep', 'category' => 'not-a-category']))
            ->assertOk()
            ->assertSee('value="Sleep"', false)
            ->assertSee('No tools match these filters');

        $this->get(route('tools.index', ['category' => 'mental-health']))
            ->assertOk()
            ->assertSee('Mental Health')
            ->assertSee('No tools match these filters');
    }
}
