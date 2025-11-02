<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Commentable;

class Speaker extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'title',
        'company',
        'bio',
        'notes',
        'contact_person',
        'email',
        'headshot_path',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_speaker');
    }

    public function contentFiles()
    {
        return $this->belongsToMany(ContentFile::class, 'content_file_speaker');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'speaker_tag');
    }

    // Helper methods
    public function getHeadshotUrlAttribute()
    {
        if ($this->headshot_path) {
            return asset('storage/' . $this->headshot_path);
        }
        return null;
    }

    public function getFullTitleAttribute()
    {
        $parts = array_filter([$this->title, $this->company]);
        return implode(' at ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
}
