<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesToolRegistry;
use App\Http\Controllers\Controller;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ToolCategoryController extends Controller
{
    use AuthorizesToolRegistry;

    public function index()
    {
        $this->authorizeToolRegistry('tools.categories');

        return view('admin.tools.categories', [
            'categories' => ToolCategory::query()
                ->withCount('tools')
                ->orderBy('display_order')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeToolRegistry('tools.categories');

        ToolCategory::query()->create($this->validated($request));

        return redirect()
            ->route('admin.tool-categories.index')
            ->with('success', 'Tool category created.');
    }

    public function update(Request $request, ToolCategory $toolCategory)
    {
        $this->authorizeToolRegistry('tools.categories');

        $toolCategory->update($this->validated($request, $toolCategory));

        return redirect()
            ->route('admin.tool-categories.index')
            ->with('success', 'Tool category updated. Linked tools were kept.');
    }

    public function destroy(ToolCategory $toolCategory)
    {
        $this->authorizeToolRegistry('tools.categories');

        if ($toolCategory->tools()->exists()) {
            return back()->withErrors([
                'category' => 'Deactivate this category instead. Deleting it is blocked while tools are linked.',
            ]);
        }

        $toolCategory->delete();

        return redirect()
            ->route('admin.tool-categories.index')
            ->with('success', 'Empty tool category removed.');
    }

    private function validated(Request $request, ?ToolCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'alpha_dash', 'max:120', Rule::unique('tool_categories', 'slug')->ignore($category?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
