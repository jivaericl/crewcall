@props([
    'sticky' => false,
])

@php
$classes = $sticky ? 'sticky top-0 z-20' : '';
@endphp

<thead {{ $attributes->class($classes) }} data-flux-columns>
    <tr>
        {{ $slot }}
    </tr>
</thead>
