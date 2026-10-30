<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesToolRegistry;
use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ToolRegistryController extends Controller
{
    use AuthorizesToolRegistry;

    public function index(Request $request)
    {
        $this->authorizeToolRegistry('tools.all');

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:tool_categories,id'],
            'status' => ['nullable', Rule::in(Tool::STATUSES)],
        ]);

        $tools = Tool::query()
            ->with('category')
            ->when($filters['q'] ?? null, function ($query, $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('module_key', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('tool_category_id', $category))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.tools.index', [
            'tools' => $tools,
            'categories' => ToolCategory::query()->orderBy('display_order')->orderBy('name')->get(),
            'filters' => $filters,
            'statuses' => Tool::STATUSES,
        ]);
    }

    public function edit(Tool $tool)
    {
        $this->authorizeToolRegistry('tools.all');

        return view('admin.tools.edit', [
            'tool' => $tool->load('category'),
            'categories' => ToolCategory::query()->orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Tool $tool)
    {
        $this->authorizeToolRegistry('tools.all');

        $data = $this->validated($request, $tool);
        $tool->update($data);

        return redirect()
            ->route('admin.tools.index')
            ->with('success', $tool->name.' was updated.');
    }

    public function toggle(Tool $tool)
    {
        $this->authorizeToolRegistry('tools.all');

        if (!$tool->is_implemented) {
            return back()->withErrors([
                'status' => $tool->name.' is '.$tool->statusLabel().' and cannot be enabled until its workflow exists.',
            ]);
        }

        $tool->update([
            'status' => $tool->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', $tool->name.' is now '.$tool->statusLabel().'.');
    }

    private function validated(Request $request, Tool $tool): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash', 'max:120', Rule::unique('tools', 'slug')->ignore($tool->id)],
            'module_key' => ['required', 'alpha_dash', 'max:60'],
            'tool_category_id' => ['nullable', 'integer', 'exists:tool_categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9 _-]+$/'],
            'access_type' => ['required', Rule::in(Tool::ACCESS_TYPES)],
            'status' => ['required', Rule::in(Tool::STATUSES)],
            'display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'route_name' => ['nullable', 'string', 'max:120'],
        ]);

        if (!$tool->is_implemented && !in_array($data['status'], ['coming_soon', 'unavailable'], true)) {
            abort(422, 'Unimplemented tools can only stay Coming Soon or Unavailable.');
        }

        if (!$tool->is_implemented) {
            $data['route_name'] = null;
        }

        return $data;
    }
}
