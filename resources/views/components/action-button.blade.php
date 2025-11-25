@props([
    'action',  // view, edit, delete, duplicate, etc.
    'href' => null,
    'wire' => null,
    'variant' => 'ghost',
    'size' => 'sm',
    'tooltip' => null,
])

@php
    $iconAlias = "actions.{$action}";
    $tooltipText = $tooltip ?? ucfirst(str_replace('-', ' ', $action));
@endphp

@if($href)
    <flux:button 
        :href="$href" 
        variant="{{ $variant }}" 
        size="{{ $size }}"
        icon-trailing="{{ $action === 'go-to' }}"
        title="{{ $tooltipText }}"
    >
        <x-lineicon alias="{{ $iconAlias }}" />
    </flux:button>
@elseif($wire)
    <flux:button 
        wire:click="{{ $wire }}" 
        variant="{{ $variant }}" 
        size="{{ $size }}"
        title="{{ $tooltipText }}"
    >
        <x-lineicon alias="{{ $iconAlias }}" />
    </flux:button>
@else
    <flux:button 
        variant="{{ $variant }}" 
        size="{{ $size }}"
        title="{{ $tooltipText }}"
        {{ $attributes }}
    >
        <x-lineicon alias="{{ $iconAlias }}" />
    </flux:button>
@endif
