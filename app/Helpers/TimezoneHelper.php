<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TimezoneHelper
{
    /**
     * Convert a datetime from event timezone to user's timezone
     *
     * @param  \Carbon\Carbon|string|null  $datetime
     * @param  string  $eventTimezone
     * @param  string|null  $userTimezone
     * @return \Carbon\Carbon|null
     */
    public static function convertToUserTimezone($datetime, string $eventTimezone, ?string $userTimezone = null): ?Carbon
    {
        if (!$datetime) {
            return null;
        }

        // Get user's timezone (use authenticated user's timezone if not provided)
        if (!$userTimezone && Auth::check()) {
            $userTimezone = Auth::user()->timezone ?? 'UTC';
        }
        
        $userTimezone = $userTimezone ?? 'UTC';

        // Convert string to Carbon if needed
        if (is_string($datetime)) {
            $datetime = Carbon::parse($datetime, $eventTimezone);
        } elseif ($datetime instanceof Carbon) {
            $datetime = $datetime->copy()->setTimezone($eventTimezone);
        }

        // Convert to user's timezone
        return $datetime->setTimezone($userTimezone);
    }

    /**
     * Format a datetime with timezone information
     *
     * @param  \Carbon\Carbon|string|null  $datetime
     * @param  string  $eventTimezone
     * @param  string|null  $userTimezone
     * @param  string  $format
     * @return string
     */
    public static function formatWithTimezone($datetime, string $eventTimezone, ?string $userTimezone = null, string $format = 'M j, Y g:i A'): string
    {
        $converted = self::convertToUserTimezone($datetime, $eventTimezone, $userTimezone);
        
        if (!$converted) {
            return '—';
        }

        // Get user's timezone
        if (!$userTimezone && Auth::check()) {
            $userTimezone = Auth::user()->timezone ?? 'UTC';
        }
        $userTimezone = $userTimezone ?? 'UTC';

        // If user timezone is different from event timezone, show both
        if ($userTimezone !== $eventTimezone) {
            $userTime = $converted->format($format);
            $eventTime = $converted->copy()->setTimezone($eventTimezone)->format($format);
            $userTz = $converted->format('T');
            $eventTz = $converted->copy()->setTimezone($eventTimezone)->format('T');
            
            return "{$userTime} {$userTz} ({$eventTime} {$eventTz})";
        }

        return $converted->format($format) . ' ' . $converted->format('T');
    }

    /**
     * Format a time (without date) with timezone information
     *
     * @param  \Carbon\Carbon|string|null  $datetime
     * @param  string  $eventTimezone
     * @param  string|null  $userTimezone
     * @return string
     */
    public static function formatTimeWithTimezone($datetime, string $eventTimezone, ?string $userTimezone = null): string
    {
        return self::formatWithTimezone($datetime, $eventTimezone, $userTimezone, 'g:i A');
    }
}
