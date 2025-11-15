<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotel extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'event_id',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'maps_link',
        'website',
        'email',
        'contact_person',
        'phone',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the event this hotel belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the hotel reservations associated with this hotel.
     */
    public function hotelReservations(): HasMany
    {
        return $this->hasMany(HotelReservation::class);
    }

    /**
     * Get the user who created the hotel.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the hotel.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the full address of the hotel.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hotel) {
            if (auth()->check()) {
                $hotel->created_by = auth()->id();
                $hotel->updated_by = auth()->id();
            }
        });

        static::updating(function ($hotel) {
            if (auth()->check()) {
                $hotel->updated_by = auth()->id();
            }
        });
    }
}
