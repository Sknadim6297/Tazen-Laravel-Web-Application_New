<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ToolsCatalog
{
    public const TOPICS = ['Anxiety', 'Meditation', 'Sleep', 'Self Care', 'Career', 'Nutrition'];

    public function browse(Request $request): array
    {
        $filters = $this->filters($request);

        return [
            'items' => new LengthAwarePaginator([], 0, 12, 1, [
                'path' => route('tools.index'),
                'query' => $request->query(),
            ]),
            'filters' => $filters,
            'categories' => ExploreCatalog::CATEGORIES,
            'topics' => self::TOPICS,
            'catalogueAvailable' => false,
        ];
    }

    private function filters(Request $request): array
    {
        $q = trim(strip_tags((string) $request->query('q', '')));
        $q = Str::limit($q, 100, '');

        $category = (string) $request->query('category', '');
        if (!array_key_exists($category, ExploreCatalog::CATEGORIES)) {
            $category = '';
        }

        return [
            'q' => $q,
            'category' => $category,
        ];
    }
}
