@props(['datetime', 'eventTimezone', 'format' => 'M j, Y g:i A', 'timeOnly' => false])

@php
    use App\Helpers\TimezoneHelper;
    
    if ($timeOnly) {
        $formatted = TimezoneHelper::formatTimeWithTimezone($datetime, $eventTimezone);
    } else {
        $formatted = TimezoneHelper::formatWithTimezone($datetime, $eventTimezone, null, $format);
    }
@endphp

<span {{ $attributes }}>{{ $formatted }}</span>
