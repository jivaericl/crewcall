<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionState extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'active_segment_id',
        'updated_by',
    ];

    /**
     * Get the session that owns this state.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the currently active segment.
     */
    public function activeSegment()
    {
        return $this->belongsTo(Segment::class, 'active_segment_id');
    }

    /**
     * Get the user who last updated the state.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Set the active segment for a session.
     */
    public static function setActiveSegment(int $sessionId, ?int $segmentId, int $userId): self
    {
        return static::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'active_segment_id' => $segmentId,
                'updated_by' => $userId,
            ]
        );
    }

    /**
     * Get the active segment ID for a session.
     */
    public static function getActiveSegmentId(int $sessionId): ?int
    {
        return static::where('session_id', $sessionId)->value('active_segment_id');
    }

    /**
     * Clear the active segment for a session.
     */
    public static function clearActiveSegment(int $sessionId): void
    {
        static::where('session_id', $sessionId)->update(['active_segment_id' => null]);
    }
}
