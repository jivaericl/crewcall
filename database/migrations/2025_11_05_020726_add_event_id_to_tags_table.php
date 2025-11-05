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
        Schema::table('tags', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->onDelete('cascade');
            $table->dropUnique(['name']); // Remove unique constraint on name alone
            $table->unique(['event_id', 'name']); // Make name unique per event
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(['event_id', 'name']);
            $table->unique('name');
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });
    }
};
