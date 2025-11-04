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
        Schema::create('user_presence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('offline'); // online, offline, away
            $table->string('current_page')->nullable(); // What page they're on
            $table->timestamp('last_seen_at');
            $table->timestamps();
            
            $table->unique(['event_id', 'user_id']);
            $table->index(['event_id', 'status', 'last_seen_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_presence');
    }
};
