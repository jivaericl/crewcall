<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'company',
        'title',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'type',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['full_name'];

    /**
     * Get the event this contact belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created the contact.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the contact.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the contact's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the contact's full name with company.
     */
    public function getFullNameWithCompanyAttribute(): string
    {
        $name = $this->full_name;
        if ($this->company) {
            $name .= ' (' . $this->company . ')';
        }
        return $name;
    }

    /**
     * Scope to filter by contact type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get only active contacts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only clients.
     */
    public function scopeClients($query)
    {
        return $query->where('type', 'client');
    }

    /**
     * Scope to get only producers.
     */
    public function scopeProducers($query)
    {
        return $query->where('type', 'producer');
    }

    /**
     * Scope to order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('last_name')->orderBy('first_name');
    }

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            if (auth()->check()) {
                $contact->created_by = auth()->id();
                $contact->updated_by = auth()->id();
            }
        });

        static::updating(function ($contact) {
            if (auth()->check()) {
                $contact->updated_by = auth()->id();
            }
        });
    }
}
