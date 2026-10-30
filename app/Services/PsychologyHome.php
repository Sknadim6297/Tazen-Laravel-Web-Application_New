<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Tool;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class PsychologyHome
{
    public function present(): array
    {
        $cards = $this->cards();

        return [
            'cards' => $cards,
            'mood' => $cards[0],
            'psychologistsUrl' => $this->psychologistsUrl(),
        ];
    }

    private function cards(): array
    {
        $definitions = [
            'daily-mood-check-in' => [
                'name' => 'Daily Mood Check-in',
                'description' => 'Understand and track your emotions.',
                'action' => 'Start check-in',
                'tone' => 'sage',
                'icon' => 'mood',
            ],
            'digital-journal' => [
                'name' => 'Digital Journal',
                'description' => 'Express your thoughts in your own words.',
                'action' => 'Open journal',
                'tone' => 'yellow',
                'icon' => 'journal',
            ],
            'gratitude-journal' => [
                'name' => 'Gratitude Journal',
                'description' => 'Notice and appreciate the good in your day.',
                'action' => 'Open gratitude',
                'tone' => 'blush',
                'icon' => 'gratitude',
            ],
        ];

        $records = $this->registryTools(array_keys($definitions));

        $cards = [];
        foreach ($definitions as $slug => $copy) {
            $tool = $records->get($slug);
            $launch = $tool?->canLaunch() === true;
            $closed = $tool && in_array($tool->status, ['inactive', 'unavailable'], true);
            $href = null;
            $action = 'Coming soon';
            $state = 'coming_soon';

            if ($closed) {
                $action = 'Unavailable';
                $state = 'unavailable';
            } elseif ($launch) {
                $href = route($tool->route_name);
                $action = $copy['action'];
                $state = 'available';
            } elseif ($slug === 'daily-mood-check-in') {
                $href = route('tools.psychology.mood');
                $action = $copy['action'];
                $state = 'available';
            }

            $cards[] = [
                'slug' => $slug,
                'name' => $copy['name'],
                'description' => $copy['description'],
                'action' => $action,
                'href' => $href,
                'tone' => $copy['tone'],
                'icon' => $copy['icon'],
                'state' => $state,
            ];
        }

        return $cards;
    }

    private function registryTools(array $slugs)
    {
        try {
            if (!Schema::hasTable('tools')) {
                return collect();
            }

            return Tool::query()->whereIn('slug', $slugs)->get()->keyBy('slug');
        } catch (QueryException) {
            return collect();
        }
    }

    public function psychologistsUrl(): string
    {
        try {
            if (Schema::hasTable('services')) {
                $service = Service::query()->where('slug', 'best-online-psychologists-india')->first()
                    ?? Service::query()->where('name', 'like', '%psycholog%')->orderBy('id')->first();

                if ($service && filled($service->slug)) {
                    return route('professionals', ['service' => $service->slug]);
                }
            }
        } catch (QueryException) {
            return route('gridlisting', ['category' => 'mental-health', 'type' => 'experts']);
        }

        return route('gridlisting', ['category' => 'mental-health', 'type' => 'experts']);
    }
}
