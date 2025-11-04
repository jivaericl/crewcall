<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_super_admin', 'first_name', 'last_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_super_admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the events this user is assigned to.
     */
    public function assignedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot(['role_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Get the roles assigned to this user for events.
     */
    public function eventRoles()
    {
        return $this->belongsToMany(Role::class, 'event_user')
            ->withPivot(['event_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Check if user is an admin for a specific event.
     */
    public function isEventAdmin(Event $event): bool
    {
        return $this->assignedEvents()
            ->where('event_id', $event->id)
            ->wherePivot('is_admin', true)
            ->exists();
    }

    /**
     * Get user's role for a specific event.
     */
    public function getRoleForEvent(Event $event)
    {
        return $this->eventRoles()
            ->where('event_id', $event->id)
            ->first();
    }
}
