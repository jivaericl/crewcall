@props([
    'sortable' => false,
    'sorted' => false,
    'align' => 'start',
    'sticky' => false,
])

@php
$alignClasses = match($align) {
    'center' => 'text-center',
    'end', 'right' => 'text-right',
    default => 'text-left',
};

$classes = [
    'py-3 px-3 first:ps-0 last:pe-0',
    'text-sm font-medium text-zinc-800 dark:text-white',
    'border-b border-zinc-800/10 dark:border-white/20',
    $alignClasses,
    $sticky ? 'sticky left-0 z-10 bg-white dark:bg-zinc-900' : '',
];
@endphp

<th {{ $attributes->class($classes) }} data-flux-column>
    {{ $slot }}
</th>
