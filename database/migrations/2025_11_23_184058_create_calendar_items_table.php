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
        Schema::create('calendar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            
            // Calendar item type: milestone, out_of_office, call
            $table->enum('type', ['milestone', 'out_of_office', 'call'])->index();
            
            // Basic information
            $table->string('title');
            $table->text('description')->nullable();
            
            // Date and time
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('all_day')->default(false);
            
            // Location (for calls/meetings)
            $table->string('location')->nullable();
            
            // Color for calendar display
            $table->string('color', 7)->nullable(); // Hex color code
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['event_id', 'start_date']);
            $table->index(['event_id', 'type']);
        });
        
        // Pivot table for calendar item users
        Schema::create('calendar_item_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['calendar_item_id', 'user_id']);
        });
        
        // Pivot table for calendar item clients
        Schema::create('calendar_item_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['calendar_item_id', 'client_id']);
        });
        
        // Pivot table for calendar item speakers
        Schema::create('calendar_item_speaker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('speaker_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['calendar_item_id', 'speaker_id']);
        });
        
        // Pivot table for calendar item tags
        Schema::create('calendar_item_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['calendar_item_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_item_tag');
        Schema::dropIfExists('calendar_item_speaker');
        Schema::dropIfExists('calendar_item_client');
        Schema::dropIfExists('calendar_item_user');
        Schema::dropIfExists('calendar_items');
    }
};
