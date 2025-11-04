<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Commentable;
use App\Traits\EventScoped;

class Session extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable, EventScoped;

    protected $table = 'event_sessions';

    protected $fillable = [
        'event_id',
        'name',
        'code',
        'description',
        'location',
        'start_date',
        'end_date',
        'client_id',
        'producer_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            $session->created_by = auth()->id();
            $session->updated_by = auth()->id();
        });

        static::updating(function ($session) {
            $session->updated_by = auth()->id();
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    public function producer()
    {
        return $this->belongsTo(Contact::class, 'producer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customFieldValues()
    {
        return $this->hasMany(SessionCustomFieldValue::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function segments()
    {
        return $this->hasMany(Segment::class);
    }

    // Helper method to get custom field value
    public function getCustomFieldValue($customFieldId)
    {
        $value = $this->customFieldValues()
            ->where('custom_field_id', $customFieldId)
            ->first();
        
        return $value ? $value->value : null;
    }

    // Helper method to set custom field value
    public function setCustomFieldValue($customFieldId, $value)
    {
        return $this->customFieldValues()->updateOrCreate(
            ['custom_field_id' => $customFieldId],
            ['value' => $value]
        );
    }

    // Scope for ordering by start date
    public function scopeOrdered($query)
    {
        return $query->orderBy('start_date', 'asc');
    }

    // Scope for filtering by event
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
}
