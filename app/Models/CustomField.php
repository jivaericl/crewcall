<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'model_type',
        'name',
        'field_type',
        'options',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    // Field types
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';
    const TYPE_SELECT = 'select';
    const TYPE_CHECKBOX = 'checkbox';

    public static function getFieldTypes()
    {
        return [
            self::TYPE_TEXT => 'Text',
            self::TYPE_NUMBER => 'Number',
            self::TYPE_DATE => 'Date',
            self::TYPE_SELECT => 'Select (Dropdown)',
            self::TYPE_CHECKBOX => 'Checkbox',
        ];
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function sessionValues()
    {
        return $this->hasMany(SessionCustomFieldValue::class);
    }

    public function values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    // Scope for ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // Scope for filtering by event
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    // Scope for filtering by model type
    public function scopeForModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    // Get available model types
    public static function getModelTypes()
    {
        return [
            'event' => 'Event',
            'session' => 'Session',
            'segment' => 'Segment',
            'cue' => 'Cue',
            'content' => 'Content',
            'contact' => 'Contact',
            'speaker' => 'Speaker',
        ];
    }
}
