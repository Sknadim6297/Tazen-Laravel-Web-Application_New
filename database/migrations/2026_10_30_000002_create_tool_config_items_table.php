<?php

use App\Services\PsychologyConfigRegistrar;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tool_config_items')) {
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
                $table->unique(['tool_id', 'group_key', 'item_key']);
                $table->index(['tool_id', 'group_key']);
            });
        }

        $this->registerMenu();

        if (Schema::hasTable('tools') && Schema::hasTable('tool_config_items')) {
            app(PsychologyConfigRegistrar::class)->sync();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_config_items');

        if (Schema::hasTable('admin_menus')) {
            DB::table('admin_menus')->where('name', 'tools.psychology')->delete();
        }
    }

    private function registerMenu(): void
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

        $exists = DB::table('admin_menus')->where('name', 'tools.psychology')->exists();
        if ($exists) {
            return;
        }

        DB::table('admin_menus')->insert([
            'name' => 'tools.psychology',
            'display_name' => 'Psychology',
            'route_name' => 'admin.psychology.settings.edit',
            'icon' => null,
            'parent_id' => $parentId,
            'order' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
