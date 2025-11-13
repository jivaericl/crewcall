<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ContentFileVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_file_id',
        'version_number',
        'file_path',
        'file_size',
        'mime_type',
        'metadata',
        'change_notes',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    // Relationships
    public function contentFile()
    {
        return $this->belongsTo(ContentFile::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper methods
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute()
    {
        // For text-based content stored in metadata
        if ($this->file_path === null && isset($this->metadata['content'])) {
            return route('content.version.download', $this->id);
        }
        
        return Storage::url($this->file_path);
    }
}
