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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Role name
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->text('description')->nullable(); // Role description
            $table->boolean('is_system')->default(false); // System-wide role (managed by super admin)
            $table->boolean('is_active')->default(true); // Can be enabled/disabled by super admin
            $table->boolean('can_add')->default(false); // Permission to add/create
            $table->boolean('can_edit')->default(false); // Permission to edit/update
            $table->boolean('can_view')->default(true); // Permission to view
            $table->boolean('can_delete')->default(false); // Permission to delete
            $table->integer('sort_order')->default(0); // For ordering roles in lists
            $table->timestamps();
            
            $table->index('is_system');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
