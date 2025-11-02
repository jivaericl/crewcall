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
        Schema::create('content_file_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_file_id')->constrained()->onDelete('cascade');
            $table->integer('version_number');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('mime_type');
            $table->json('metadata')->nullable();
            $table->text('change_notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('content_file_id');
            $table->index(['content_file_id', 'version_number']);
            $table->unique(['content_file_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_file_versions');
    }
};
