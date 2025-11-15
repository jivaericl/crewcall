<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'travel_id',
        'airline',
        'flight_number',
        'departure_airport',
        'departure_time',
        'arrival_airport',
        'arrival_time',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    /**
     * Get the travel record this flight belongs to.
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * Get the user (team member) this flight belongs to through travel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get the user who created the flight.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the flight.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flight) {
            if (auth()->check()) {
                $flight->created_by = auth()->id();
                $flight->updated_by = auth()->id();
            }
        });

        static::updating(function ($flight) {
            if (auth()->check()) {
                $flight->updated_by = auth()->id();
            }
        });
    }
}
