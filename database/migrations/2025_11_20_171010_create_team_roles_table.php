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
        Schema::create('team_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Lighting Director", "Production Assistant"
            $table->text('description')->nullable();
            $table->string('color')->default('#3B82F6'); // For UI display
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Ensure unique role names per event
            $table->unique(['event_id', 'name']);
        });

        // Pivot table for assigning roles to users
        Schema::create('event_user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // A user can only have one instance of a specific role per event
            $table->unique(['event_id', 'user_id', 'team_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user_roles');
        Schema::dropIfExists('team_roles');
    }
};
