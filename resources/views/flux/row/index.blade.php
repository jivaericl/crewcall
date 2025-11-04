@props([
    'variant' => 'default',
])

@php
$classes = [
    'border-b border-zinc-200 dark:border-zinc-700 last:border-0',
    'hover:bg-zinc-50 dark:hover:bg-zinc-800/50',
    'transition-colors duration-150',
];
@endphp

<tr {{ $attributes->class($classes) }} data-flux-row>
    {{ $slot }}
</tr>
