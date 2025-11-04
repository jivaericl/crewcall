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
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('event_sessions')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('producer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('session_id');
            $table->index('start_time');
            $table->index(['session_id', 'start_time']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
