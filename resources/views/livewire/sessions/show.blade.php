<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Run of Show: {{ $session->name }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $session->start_date ? $session->start_date->format('F j, Y g:i A') : 'No date set' }}
                    @if($session->end_date)
                        - {{ $session->end_date->format('g:i A') }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('events.sessions.edit', [$eventId, $sessionId]) }}" variant="ghost">
                    Edit Session
                </flux:button>
                <flux:button href="{{ route('events.sessions.index', $eventId) }}" variant="ghost">
                    Back to Sessions
                </flux:button>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $session->client?->full_name ?? 'Not assigned' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Producer</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $session->producer?->full_name ?? 'Not assigned' }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Segments</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $segments->count() }} segment(s)
                            </p>
                        </div>
                    </div>
                    @if($session->description)
                        <div class="mt-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $session->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search -->
                        <div>
                            <flux:input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                placeholder="Search segments and cues..."
                                class="w-full"
                            />
                        </div>

                        <!-- Cue Type Filter -->
                        <div>
                            <flux:select wire:model.live="filterCueType">
                                <option value="">All Cue Types</option>
                                @foreach($cueTypes as $cueType)
                                    <option value="{{ $cueType->id }}">{{ $cueType->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Run of Show Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($segments->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No segments</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new segment.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" style="width: 80px;"></th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Operator</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($segments as $segment)
                                        <!-- Segment Header Row -->
                                        <tr class="bg-blue-50 dark:bg-blue-900/20">
                                            <td class="px-3 py-3" colspan="6">
                                                <div class="flex items-center gap-3">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">
                                                        SEGMENT
                                                    </span>
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $segment->name }}
                                                    </span>
                                                    @if($segment->start_time)
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            Start: {{ $segment->start_time }}
                                                        </span>
                                                    @endif
                                                    @if($segment->duration)
                                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                                            Duration: {{ $segment->duration }} min
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($segment->description)
                                                    <p class="mt-1 ml-20 text-xs text-gray-600 dark:text-gray-400">{{ $segment->description }}</p>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Cues for this Segment -->
                                        @if($segment->cues->isNotEmpty())
                                            @foreach($segment->cues as $cue)
                                                <tr class="
                                                    @if($cue->status === 'go') bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30
                                                    @elseif($cue->status === 'complete') bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600
                                                    @else hover:bg-gray-50 dark:hover:bg-gray-700
                                                    @endif">
                                                    <!-- GO Button -->
                                                    <td class="px-3 py-3 text-center align-top">
                                                        <button 
                                                            wire:click="activateCue({{ $cue->id }})" 
                                                            class="px-3 py-1 text-xs font-bold rounded
                                                                @if($cue->status === 'go') 
                                                                    bg-green-600 text-white
                                                                @elseif($cue->status === 'complete')
                                                                    bg-gray-500 dark:bg-gray-600 text-white hover:bg-gray-600 dark:hover:bg-gray-500
                                                                @else 
                                                                    bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-400 dark:hover:bg-gray-500
                                                                @endif"
                                                            type="button">
                                                            GO
                                                        </button>
                                                    </td>
                                                    
                                                    <!-- Time & Code -->
                                                    <td class="px-6 py-3 align-top text-sm text-gray-900 dark:text-gray-100">
                                                        <div>{{ $cue->time ? $cue->time->format('g:i A') : '-' }}</div>
                                                        @if($cue->code)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cue->code }}</div>
                                                        @endif
                                                    </td>
                                                    
                                                    <!-- Cue Name -->
                                                    <td class="px-6 py-3 align-top">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cue->name }}</div>
                                                        @if($cue->description)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cue->description }}</div>
                                                        @endif
                                                        @if($cue->filename)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                📁 {{ $cue->filename }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    
                                                    <!-- Type -->
                                                    <td class="px-6 py-3 align-top whitespace-nowrap">
                                                        @if($cue->cueType)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                                  style="background-color: {{ $cue->cueType->color }}20; color: {{ $cue->cueType->color }};">
                                                                {{ $cue->cueType->name }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    
                                                    <!-- Operator -->
                                                    <td class="px-6 py-3 align-top whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $cue->operator?->name ?: '-' }}
                                                    </td>
                                                    
                                                    <!-- Notes -->
                                                    <td class="px-6 py-3 align-top text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $cue->notes }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No cues in this segment
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
