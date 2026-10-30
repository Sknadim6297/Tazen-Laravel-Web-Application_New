<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('psychology_mood_checkins')) {
            return;
        }

        Schema::create('psychology_mood_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('completion_key')->unique();
            $table->string('mood_key', 40);
            $table->json('emotion_keys');
            $table->text('reflection')->nullable();
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamps();
            $table->index(['user_id', 'checked_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_mood_checkins');
    }
};
