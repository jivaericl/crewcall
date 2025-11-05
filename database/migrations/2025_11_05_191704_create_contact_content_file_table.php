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
        Schema::create('contact_content_file', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_file_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['contact_id', 'content_file_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_content_file');
    }
};
