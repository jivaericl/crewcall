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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Model class name
            $table->unsignedBigInteger('auditable_id'); // Model ID
            $table->string('event'); // created, updated, deleted, restored
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who made the change
            $table->json('old_values')->nullable(); // Before values
            $table->json('new_values')->nullable(); // After values
            $table->string('ip_address')->nullable(); // User's IP address
            $table->string('user_agent')->nullable(); // User's browser/agent
            $table->timestamps();
            
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('event');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
