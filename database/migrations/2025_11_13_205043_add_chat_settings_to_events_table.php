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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('chat_enabled')->default(true)->after('timezone');
            $table->boolean('chat_notifications_enabled')->default(true)->after('chat_enabled');
            $table->boolean('chat_sound_enabled')->default(true)->after('chat_notifications_enabled');
            $table->boolean('chat_desktop_notifications')->default(false)->after('chat_sound_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'chat_enabled',
                'chat_notifications_enabled',
                'chat_sound_enabled',
                'chat_desktop_notifications',
            ]);
        });
    }
};
