<div x-data="{ clockInterval: null }" x-init="clockInterval = setInterval(() => $wire.updateClock(), 1000)" x-destroy="clearInterval(clockInterval)">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Show Calling - {{ $event->name }}
                        </h2>
                        @if($selectedSession)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $selectedSession->name }} • {{ \Carbon\Carbon::parse($selectedSession->start_date)->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">
                            {{ $clockTime }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Current Time
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Session Selector & Controls -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex gap-2 items-center flex-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Session:</label>
                        <select wire:model.live="selectedSessionId" wire:change="selectSession($event.target.value)" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}">{{ $session->name }} - {{ \Carbon\Carbon::parse($session->start_date)->format('M d') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <flux:button wire:click="setViewMode('table')" variant="{{ $viewMode === 'table' ? 'primary' : 'ghost' }}" size="sm">
                            Table View
                        </flux:button>
                        <flux:button wire:click="setViewMode('timeline')" variant="{{ $viewMode === 'timeline' ? 'primary' : 'ghost' }}" size="sm">
                            Timeline View
                        </flux:button>
                    </div>

                    <div class="flex gap-2 items-center">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" wire:model.live="showCompleted" class="rounded">
                            Show Completed
                        </label>
                        <flux:button wire:click="resetFilters" variant="ghost" size="sm">
                            Reset Filters
                        </flux:button>
                    </div>
                </div>

                <!-- Filters -->
                @if($selectedSession)
                    <div class="mt-4 flex gap-4">
                        <div class="flex-1">
                            <label class="text-xs text-gray-600 dark:text-gray-400">Filter by Segment:</label>
                            <select wire:model.live="filterSegmentId" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                <option value="">All Segments</option>
                                @foreach($segments as $segment)
                                    <option value="{{ $segment->id }}">{{ $segment->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-600 dark:text-gray-400">Filter by Cue Type:</label>
                            <select wire:model.live="filterCueType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                <option value="">All Types</option>
                                @foreach($cueTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>

            @if($selectedSession)
                <!-- Table View -->
                @if($viewMode === 'table')
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Segment</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cue #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Operator</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($cues as $cue)
                                    <tr class="
                                            @if($standbyCueId === $cue->id) bg-yellow-50 dark:bg-yellow-900/20 @endif
                                            @if($cue->status === 'complete') bg-green-50 dark:bg-green-900/20 @endif
                                            @if($cue->status === 'skip') bg-gray-100 dark:bg-gray-700 @endif
                                            @if($cue->priority === 'high') border-l-4 border-red-500 @endif
                                            @if($cue->priority === 'medium') border-l-4 border-yellow-500 @endif
                                        ">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($cue->time)->format('g:i A') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $cue->segment->name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $cue->cue_number }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $cue->cueType->color }}20; color: {{ $cue->cueType->color }}">
                                                    {{ $cue->cueType->name }}
                                                </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-medium">{{ $cue->name }}</div>
                                            @if($cue->description)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($cue->description, 50) }}</div>
                                            @endif
                                            @if($cue->filename)
                                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">📁 {{ $cue->filename }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $cue->operator?->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cue->status_badge_color }}">
                                                    {{ ucfirst($cue->status) }}
                                                </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-1">
                                                @if($cue->status === 'pending')
                                                    <flux:button wire:click="setStandby({{ $cue->id }})" variant="primary" size="sm" title="Standby">
                                                        Standby
                                                    </flux:button>
                                                @endif
                                                @if($standbyCueId === $cue->id)
                                                    <flux:button wire:click="executeCue({{ $cue->id }})" variant="primary" size="sm" title="GO">
                                                        GO
                                                    </flux:button>
                                                    <flux:button wire:click="skipCue({{ $cue->id }})" variant="ghost" size="sm" title="Skip">
                                                        Skip
                                                    </flux:button>
                                                @endif
                                                @if($cue->status !== 'pending')
                                                    <flux:button wire:click="resetCue({{ $cue->id }})" variant="ghost" size="sm" title="Reset">
                                                        Reset
                                                    </flux:button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No cues found for this session.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Timeline View -->
                @if($viewMode === 'timeline')
                    @include('livewire.show-call.partials.timeline-view')
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No sessions available</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a session to start show calling.</p>
                </div>
            @endif
        </div>
    </div>
</div>
