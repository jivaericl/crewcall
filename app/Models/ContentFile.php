<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Commentable;
use App\Traits\EventScoped;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ContentFile extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable, EventScoped;

    protected $fillable = [
        'event_id',
        'category_id',
        'name',
        'slug',
        'description',
        'file_type',
        'mime_type',
        'current_file_path',
        'current_file_size',
        'current_version',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'current_file_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            if (empty($file->slug)) {
                $file->slug = Str::slug($file->name) . '-' . Str::random(8);
            }
            $file->created_by = auth()->id();
        });

        static::updating(function ($file) {
            $file->updated_by = auth()->id();
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class);
    }

    public function versions()
    {
        return $this->hasMany(ContentFileVersion::class)->orderByDesc('version_number');
    }

    public function currentVersion()
    {
        return $this->hasOne(ContentFileVersion::class)->where('version_number', $this->current_version);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'content_file_speaker')
            ->withTimestamps();
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'content_file_segment')
            ->withTimestamps();
    }

    public function cues()
    {
        return $this->belongsToMany(Cue::class, 'content_file_cue')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'content_file_tag')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('created_at');
    }

    // Helper methods
    public function getFileTypeIconAttribute()
    {
        return match($this->file_type) {
            'audio' => '🎵',
            'video' => '🎬',
            'presentation' => '📊',
            'document' => '📄',
            'image' => '🖼️',
            default => '📁',
        };
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->current_file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->current_file_path);
    }

    public function createNewVersion($filePath, $fileSize, $mimeType, $metadata = [], $changeNotes = null)
    {
        $newVersionNumber = $this->current_version + 1;

        $version = ContentFileVersion::create([
            'content_file_id' => $this->id,
            'version_number' => $newVersionNumber,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'metadata' => $metadata,
            'change_notes' => $changeNotes,
            'uploaded_by' => auth()->id(),
        ]);

        $this->update([
            'current_file_path' => $filePath,
            'current_file_size' => $fileSize,
            'current_version' => $newVersionNumber,
            'mime_type' => $mimeType,
            'metadata' => $metadata,
        ]);

        return $version;
    }
}
