<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Admin;

trait AuthorizesToolRegistry
{
    protected function authorizeToolRegistry(string ...$menuNames): void
    {
        $admin = auth('admin')->user();
        abort_unless($admin instanceof Admin, 403);

        if ($admin->role === 'super_admin') {
            return;
        }

        $allowed = array_merge(['tools_management'], $menuNames);
        $names = $admin->menus()->pluck('name');

        abort_unless($names->intersect($allowed)->isNotEmpty(), 403);
    }
}
