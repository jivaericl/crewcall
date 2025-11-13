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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('chat_sound_enabled')->default(true)->after('profile_photo_path');
            $table->boolean('chat_widget_enabled')->default(true)->after('chat_sound_enabled');
            $table->boolean('chat_notifications_enabled')->default(true)->after('chat_widget_enabled');
            $table->boolean('chat_desktop_notifications')->default(true)->after('chat_notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'chat_sound_enabled',
                'chat_widget_enabled',
                'chat_notifications_enabled',
                'chat_desktop_notifications',
            ]);
        });
    }
};
