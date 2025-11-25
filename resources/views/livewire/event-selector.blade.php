<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    @if($selectedEvent)
        <button 
            @click="open = !open"
            type="button"
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
            <x-lineicon alias="ui.event_selector" size="w-5 h-5" />
            <span class="max-w-xs truncate">{{ $selectedEvent->name }}</span>
            <svg class="w-4 h-4 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    @else
        <button 
            @click="open = !open"
            type="button"
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
        >
            <x-lineicon alias="ui.event_selector" size="w-5 h-5" />
            <span>Select Event</span>
            <svg class="w-4 h-4 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    @endif

    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute left-0 z-50 mt-2 w-80 origin-top-left rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1 max-h-96 overflow-y-auto">
            @forelse($events as $event)
                <button
                    wire:click="selectEvent({{ $event->id }})"
                    class="w-full text-left px-4 py-3 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 {{ $selectedEventId == $event->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $event->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        @if($selectedEventId == $event->id)
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </button>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    No events found
                </div>
            @endforelse
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700">
            <a 
                href="{{ route('events.index') }}"
                class="block px-4 py-3 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    View All Events
                </div>
            </a>
        </div>
    </div>
</div>
