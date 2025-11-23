<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration drops any existing calendar tables from a partial/failed migration.
     * It runs BEFORE the main calendar_items migration to ensure a clean slate.
     */
    public function up(): void
    {
        // Drop in reverse order of dependencies
        Schema::dropIfExists('calendar_item_tag');
        Schema::dropIfExists('calendar_item_speaker');
        Schema::dropIfExists('calendar_item_client');
        Schema::dropIfExists('calendar_item_user');
        Schema::dropIfExists('calendar_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do - this is a cleanup migration
        // The main calendar migration will recreate the tables
    }
};
