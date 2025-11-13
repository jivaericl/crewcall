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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('recipient_id')->nullable()->after('user_id');
            $table->boolean('is_direct_message')->default(false)->after('recipient_id');
            $table->json('mentions')->nullable()->after('message');
            
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'recipient_id']);
            $table->index('is_direct_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['recipient_id']);
            $table->dropIndex(['user_id', 'recipient_id']);
            $table->dropIndex(['is_direct_message']);
            $table->dropColumn(['recipient_id', 'is_direct_message', 'mentions']);
        });
    }
};
