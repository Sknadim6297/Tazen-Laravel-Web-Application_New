<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('category_boxes', 'icon_name')) {
                $table->string('icon_name', 100)->nullable()->after('image');
            }

            if (!Schema::hasColumn('category_boxes', 'icon_path')) {
                $table->string('icon_path')->nullable()->after('icon_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_boxes', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('category_boxes', 'icon_path')) {
                $columns[] = 'icon_path';
            }
            if (Schema::hasColumn('category_boxes', 'icon_name')) {
                $columns[] = 'icon_name';
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
