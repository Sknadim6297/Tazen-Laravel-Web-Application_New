<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('professional_services', 'service_id')) {
            Schema::table('professional_services', function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->nullable()->after('professional_id');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
                $table->index('service_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_services', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
