@props([
    'variant' => 'info',
    'dismissible' => false,
])

@php
$classes = match($variant) {
    'success' => 'bg-green-600 text-white',
    'error', 'danger' => 'bg-red-600 text-white',
    'warning' => 'bg-yellow-500 text-white',
    'info' => 'bg-blue-600 text-white',
    default => 'bg-gray-600 text-white',
};
@endphp

<div {{ $attributes->class(['p-4 text-center font-medium', $classes]) }} role="banner">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button type="button" class="ml-4 text-white/80 hover:text-white" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
