<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $session->name }}</h2>
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

            <!-- Segments and Cues Hierarchical View -->
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
                        <div class="space-y-6">
                            @foreach($segments as $segment)
                                <!-- Segment Card -->
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <!-- Segment Header -->
                                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $segment->name }}
                                                </h3>
                                                <div class="mt-1 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                    @if($segment->start_time)
                                                        <span>Start: {{ $segment->start_time }}</span>
                                                    @endif
                                                    @if($segment->duration)
                                                        <span>Duration: {{ $segment->duration }} min</span>
                                                    @endif
                                                    <span>{{ $segment->cues->count() }} cue(s)</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($segment->description)
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $segment->description }}</p>
                                        @endif
                                    </div>

                                    <!-- Cues List -->
                                    @if($segment->cues->isNotEmpty())
                                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($segment->cues as $cue)
                                                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-900">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-3">
                                                                <!-- Cue Type Badge -->
                                                                @if($cue->cueType)
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                                          style="background-color: {{ $cue->cueType->color }}20; color: {{ $cue->cueType->color }};">
                                                                        {{ $cue->cueType->name }}
                                                                    </span>
                                                                @endif
                                                                
                                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $cue->name }}
                                                                </h4>
                                                            </div>
                                                            
                                                            @if($cue->description)
                                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                                    {{ $cue->description }}
                                                                </p>
                                                            @endif
                                                            
                                                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                                @if($cue->time)
                                                                    <span>Time: {{ $cue->time }}</span>
                                                                @endif
                                                                @if($cue->operator)
                                                                    <span>Assigned: {{ $cue->operator->name }}</span>
                                                                @endif
                                                            </div>
                                                            
                                                            @if($cue->notes)
                                                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded px-2 py-1">
                                                                    <strong>Notes:</strong> {{ $cue->notes }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No cues in this segment
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
