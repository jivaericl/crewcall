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
        Schema::table('cues', function (Blueprint $table) {
            $table->foreignId('content_file_id')->nullable()->after('filename')->constrained('content_files')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cues', function (Blueprint $table) {
            $table->dropForeign(['content_file_id']);
            $table->dropColumn('content_file_id');
        });
    }
};
