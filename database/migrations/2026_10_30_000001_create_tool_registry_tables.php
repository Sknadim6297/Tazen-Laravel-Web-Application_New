<?php

use App\Services\ToolRegistryRegistrar;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tool_categories')) {
            Schema::create('tool_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('display_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tools')) {
            Schema::create('tools', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tool_category_id')->nullable()->constrained('tool_categories')->nullOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('module_key')->index();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('access_type')->default('free');
                $table->string('status')->default('coming_soon')->index();
                $table->unsignedInteger('display_order')->default(0);
                $table->string('route_name')->nullable();
                $table->boolean('is_implemented')->default(false);
                $table->timestamps();
            });
        }

        $this->registerMenus();

        if (Schema::hasTable('tool_categories') && Schema::hasTable('tools')) {
            app(ToolRegistryRegistrar::class)->register();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
        Schema::dropIfExists('tool_categories');

        if (Schema::hasTable('admin_menus')) {
            DB::table('admin_menus')->whereIn('name', [
                'tools.all',
                'tools.categories',
                'tools.settings',
                'tools_management',
            ])->delete();
        }
    }

    private function registerMenus(): void
    {
        if (!Schema::hasTable('admin_menus')) {
            return;
        }

        $parent = DB::table('admin_menus')->where('name', 'tools_management')->first();
        if (!$parent) {
            $parentId = DB::table('admin_menus')->insertGetId([
                'name' => 'tools_management',
                'display_name' => 'Tools Management',
                'route_name' => null,
                'icon' => 'ri-tools-line',
                'parent_id' => null,
                'order' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $parentId = $parent->id;
        }

        $children = [
            ['tools.all', 'All Tools', 'admin.tools.index', 1],
            ['tools.categories', 'Tool Categories', 'admin.tool-categories.index', 2],
            ['tools.settings', 'Tool Settings', 'admin.tool-settings.edit', 3],
        ];

        foreach ($children as [$name, $label, $route, $order]) {
            $exists = DB::table('admin_menus')->where('name', $name)->exists();
            if ($exists) {
                continue;
            }
            DB::table('admin_menus')->insert([
                'name' => $name,
                'display_name' => $label,
                'route_name' => $route,
                'icon' => null,
                'parent_id' => $parentId,
                'order' => $order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
