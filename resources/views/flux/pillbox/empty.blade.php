@php
$classes = Flux::classes()
    ->add('px-2 py-1.5 text-sm text-zinc-400 dark:text-zinc-500')
    ;
@endphp

<ui-empty class="data-hidden:hidden {{ $classes }}" data-flux-listbox-empty>
    {{ $slot }}
</ui-empty>