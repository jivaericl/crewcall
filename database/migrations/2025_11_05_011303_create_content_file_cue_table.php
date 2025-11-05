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
        Schema::create('content_file_cue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_file_id')->constrained('content_files')->onDelete('cascade');
            $table->foreignId('cue_id')->constrained('cues')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['content_file_id', 'cue_id']);
            $table->index('content_file_id');
            $table->index('cue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_file_cue');
    }
};
