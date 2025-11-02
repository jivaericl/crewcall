<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'color',
        'is_system',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            if (empty($category->sort_order)) {
                $category->sort_order = static::max('sort_order') + 1;
            }
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function contentFiles()
    {
        return $this->hasMany(ContentFile::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true)->whereNull('event_id');
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where(function ($q) use ($eventId) {
            $q->where('event_id', $eventId)
              ->orWhereNull('event_id');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
