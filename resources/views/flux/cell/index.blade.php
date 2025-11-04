@props([
    'align' => 'start',
])

@php
$alignClasses = match($align) {
    'center' => 'text-center',
    'end', 'right' => 'text-right',
    default => 'text-left',
};

$classes = [
    'py-3 px-3 first:ps-0 last:pe-0',
    'text-sm text-zinc-700 dark:text-zinc-300',
    $alignClasses,
];
@endphp

<td {{ $attributes->class($classes) }} data-flux-cell>
    {{ $slot }}
</td>
