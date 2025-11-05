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
        Schema::create('contact_session', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('session_id')->constrained('event_sessions')->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['contact_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_session');
    }
};
