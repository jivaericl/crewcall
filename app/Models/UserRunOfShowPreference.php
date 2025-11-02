<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRunOfShowPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'visible_columns',
    ];

    protected $casts = [
        'visible_columns' => 'array',
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'visible_columns' => null,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->visible_columns)) {
                $model->visible_columns = static::defaultColumns();
            }
        });
    }

    /**
     * Get the user that owns the preference.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session that this preference is for.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the default visible columns.
     */
    public static function defaultColumns(): array
    {
        return [
            'order',
            'name',
            'start_time',
            'end_time',
            'duration',
            'type',
            'status',
            'notes',
        ];
    }

    /**
     * Get all available columns.
     */
    public static function availableColumns(): array
    {
        return [
            'order' => 'Order',
            'name' => 'Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'duration' => 'Duration',
            'type' => 'Type',
            'status' => 'Status',
            'notes' => 'Notes',
            'cues_count' => 'Cues',
            'created_by' => 'Created By',
            'updated_at' => 'Last Updated',
        ];
    }

    /**
     * Get or create preference for a user and session.
     */
    public static function getOrCreate(int $userId, int $sessionId): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'session_id' => $sessionId,
            ],
            [
                'visible_columns' => static::defaultColumns(),
            ]
        );
    }
}
