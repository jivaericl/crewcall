@blaze

@props([
    'placeholder' => null,
    'suffix' => null,
    'size' => null,
    'max' => null,
])

@php
    $classes = Flux::classes()
        ->add('truncate flex gap-2 text-start flex-1 text-zinc-700')
        ->add('[[disabled]_&]:text-zinc-500 dark:text-zinc-300 dark:[[disabled]_&]:text-zinc-400');

    $optionClasses = Flux::classes()
        ->add('px-2 flex text-zinc-700 dark:text-zinc-200 bg-zinc-400/15 dark:bg-zinc-400/40')
        ->add(match($size) {
            default => 'rounded-md py-1 text-base sm:text-sm leading-4',
            'sm' => 'rounded-sm py-[calc(0.125rem+1px)] text-sm leading-4',
        });

    $removeClasses = Flux::classes()
        ->add('px-1 -me-2 text-zinc-400 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-zinc-200')
        ->add(match($size) {
            default => 'py-[calc(0.25rem-1px)] -my-[calc(0.25rem-1px)]',
            'sm' => 'py-[calc(0.25rem-2px)] -my-[calc(0.25rem-2px)]',
        });
@endphp

<ui-selected x-ignore wire:ignore {{ $attributes->class($classes) }}>
    <template name="placeholder">
        <span class="ms-1 text-zinc-400 [[disabled]_&]:text-zinc-400/70 dark:text-zinc-400 dark:[[disabled]_&]:text-zinc-500" data-flux-pillbox-placeholder>
            {{ $placeholder }}
        </span>
    </template>

    <template name="option">
        <div {{ $attributes->class($optionClasses) }}>
            <div class="font-medium"><slot name="text"></slot></div>

            <ui-selected-remove {{ $attributes->class($removeClasses) }}>
                <flux:icon.x-mark variant="micro" :class="$size === 'xs' ? 'size-3' : ''" />
            </ui-selected-remove>
        </div>
    </template>

    <template name="options">
        <div class="flex flex-wrap gap-1">
            <slot></slot>
        </div>
    </template>
</ui-selected>