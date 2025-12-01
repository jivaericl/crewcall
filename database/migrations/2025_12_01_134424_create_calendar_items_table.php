<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('calendar_items')) {
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_items'); // Commented out - not created
    }
};
