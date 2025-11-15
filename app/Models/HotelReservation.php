<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelReservation extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'travel_id',
        'hotel_id',
        'reservation_number',
        'check_in_date',
        'check_out_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    /**
     * Get the travel record this hotel reservation belongs to.
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * Get the hotel this reservation is for.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the user (team member) this hotel reservation belongs to through travel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Get the user who created the hotel reservation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the hotel reservation.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the number of nights for this reservation.
     */
    public function getNightsAttribute(): int
    {
        if (!$this->check_in_date || !$this->check_out_date) {
            return 0;
        }

        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (auth()->check()) {
                $reservation->created_by = auth()->id();
                $reservation->updated_by = auth()->id();
            }
        });

        static::updating(function ($reservation) {
            if (auth()->check()) {
                $reservation->updated_by = auth()->id();
            }
        });
    }
}
