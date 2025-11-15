<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $table = 'travels';

    protected $fillable = [
        'event_id',
        'user_id',
        'is_traveling',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_traveling' => 'boolean',
    ];

    /**
     * Get the event this travel record belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user (team member) this travel record belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the flights associated with this travel record.
     */
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    /**
     * Get the hotel reservations associated with this travel record.
     */
    public function hotelReservations(): HasMany
    {
        return $this->hasMany(HotelReservation::class);
    }

    /**
     * Get the user who created the travel record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the travel record.
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

        static::creating(function ($travel) {
            if (auth()->check()) {
                $travel->created_by = auth()->id();
                $travel->updated_by = auth()->id();
            }
        });

        static::updating(function ($travel) {
            if (auth()->check()) {
                $travel->updated_by = auth()->id();
            }
        });
    }
}
