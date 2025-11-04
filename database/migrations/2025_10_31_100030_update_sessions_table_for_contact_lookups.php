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
        Schema::table('event_sessions', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['client_id']);
            $table->dropForeign(['producer_id']);
            
            // Recreate foreign keys pointing to contacts table
            $table->foreign('client_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('producer_id')->references('id')->on('contacts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            // Drop contacts foreign keys
            $table->dropForeign(['client_id']);
            $table->dropForeign(['producer_id']);
            
            // Recreate foreign keys pointing to users table
            $table->foreign('client_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('producer_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
