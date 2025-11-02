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
        Schema::create('cues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('segment_id')->constrained()->onDelete('cascade');
            $table->foreignId('cue_type_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->time('time')->nullable();
            $table->enum('status', ['standby', 'go', 'complete', 'skip'])->default('standby');
            $table->text('notes')->nullable();
            $table->string('filename')->nullable(); // For audio/video content
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('segment_id');
            $table->index('cue_type_id');
            $table->index('status');
            $table->index('time');
            $table->index(['segment_id', 'sort_order']);
            $table->index(['segment_id', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cues');
    }
};
