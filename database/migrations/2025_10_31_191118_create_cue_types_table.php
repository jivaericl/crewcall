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
        Schema::create('cue_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#3B82F6'); // Default blue color
            $table->string('icon')->nullable(); // For future icon support
            $table->boolean('is_system')->default(false); // System types vs custom types
            $table->boolean('is_active')->default(true);
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('cascade'); // Null = system-wide
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('event_id');
            $table->index('is_active');
            $table->index(['event_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cue_types');
    }
};
