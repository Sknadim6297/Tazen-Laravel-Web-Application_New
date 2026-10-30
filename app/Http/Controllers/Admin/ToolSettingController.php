<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesToolRegistry;
use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ToolSettingController extends Controller
{
    use AuthorizesToolRegistry;

    public function edit()
    {
        $this->authorizeToolRegistry('tools.settings');

        return view('admin.tools.settings', [
            'tools' => Tool::query()->with('category')->orderBy('display_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorizeToolRegistry('tools.settings');

        $data = $request->validate([
            'tools' => ['required', 'array'],
            'tools.*.id' => ['required', 'integer', 'exists:tools,id'],
            'tools.*.display_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'tools.*.access_type' => ['required', Rule::in(Tool::ACCESS_TYPES)],
            'tools.*.status' => ['nullable', Rule::in(Tool::STATUSES)],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['tools'] as $row) {
                $tool = Tool::query()->findOrFail($row['id']);
                $changes = [
                    'display_order' => $row['display_order'],
                    'access_type' => $row['access_type'],
                ];

                if ($tool->is_implemented && isset($row['status'])) {
                    $changes['status'] = $row['status'];
                }

                $tool->update($changes);
            }
        });

        return redirect()
            ->route('admin.tool-settings.edit')
            ->with('success', 'Tool settings saved. Unfinished tools stayed unavailable.');
    }
}
