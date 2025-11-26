@props(['action', 'size' => 'w-4 h-4'])

@php
    // Map action names to icon aliases from config
    $iconAlias = 'actions.' . $action;
@endphp

<x-lineicon :alias="$iconAlias" :size="$size" {{ $attributes }} />
