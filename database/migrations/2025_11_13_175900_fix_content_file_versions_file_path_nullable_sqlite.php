<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN properly, so we need to recreate the table
        // This is the proper way to modify columns in SQLite
        
        // Create new table with correct schema
        Schema::create('content_file_versions_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_file_id')->constrained('content_files')->onDelete('cascade');
            $table->integer('version_number');
            $table->string('file_path')->nullable(); // NOW NULLABLE
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->json('metadata')->nullable();
            $table->text('change_notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('content_file_id');
            $table->index(['content_file_id', 'version_number']);
            $table->unique(['content_file_id', 'version_number']);
        });
        
        // Copy data from old table to new table
        DB::statement('INSERT INTO content_file_versions_new SELECT * FROM content_file_versions');
        
        // Drop old table
        Schema::dropIfExists('content_file_versions');
        
        // Rename new table to original name
        Schema::rename('content_file_versions_new', 'content_file_versions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate with NOT NULL constraint
        Schema::create('content_file_versions_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_file_id')->constrained('content_files')->onDelete('cascade');
            $table->integer('version_number');
            $table->string('file_path'); // NOT NULLABLE
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->json('metadata')->nullable();
            $table->text('change_notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('content_file_id');
            $table->index(['content_file_id', 'version_number']);
            $table->unique(['content_file_id', 'version_number']);
        });
        
        DB::statement('INSERT INTO content_file_versions_new SELECT * FROM content_file_versions WHERE file_path IS NOT NULL');
        Schema::dropIfExists('content_file_versions');
        Schema::rename('content_file_versions_new', 'content_file_versions');
    }
};
