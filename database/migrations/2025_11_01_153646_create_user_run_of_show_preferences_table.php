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
        Schema::create('user_run_of_show_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->json('visible_columns')->default(json_encode([
                'order',
                'name',
                'start_time',
                'end_time',
                'duration',
                'type',
                'status',
                'notes'
            ]));
            $table->timestamps();

            // Unique constraint: one preference record per user per session
            $table->unique(['user_id', 'session_id']);
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_run_of_show_preferences');
    }
};
