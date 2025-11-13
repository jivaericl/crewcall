<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ContentFile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing text-based content from 'content' field to 'metadata'
        $textContent = ContentFile::whereIn('file_type', ['plain_text', 'rich_text', 'url'])
            ->whereNotNull('content')
            ->get();
        
        foreach ($textContent as $content) {
            $metadata = $content->metadata ?? [];
            
            // Only migrate if content field has data and metadata doesn't
            if (!empty($content->content) && empty($metadata['content'])) {
                $metadata['content'] = $content->content;
                $content->update(['metadata' => $metadata]);
                
                // Create initial version if none exists
                if ($content->versions()->count() === 0) {
                    \App\Models\ContentFileVersion::create([
                        'content_file_id' => $content->id,
                        'version_number' => 1,
                        'file_path' => null,
                        'file_size' => strlen($content->content),
                        'mime_type' => $content->mime_type,
                        'metadata' => [
                            'content' => $content->content,
                        ],
                        'change_notes' => 'Migrated from content field',
                        'uploaded_by' => $content->uploaded_by ?? 1,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally restore content from metadata back to content field
        $textContent = ContentFile::whereIn('file_type', ['plain_text', 'rich_text', 'url'])->get();
        
        foreach ($textContent as $content) {
            if (isset($content->metadata['content'])) {
                $content->update(['content' => $content->metadata['content']]);
            }
        }
    }
};
