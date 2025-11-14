<div class="space-y-4">
    @foreach($segments as $segment)
        @php
            $segmentCues = $cues->where('segment_id', $segment->id);
        @endphp
        @if($segmentCues->count() > 0 || !$filterSegmentId)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <!-- Segment Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $segment->name }}</h3>
                            <p class="text-sm text-blue-100">
                                {{ \Carbon\Carbon::parse($segment->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($segment->end_time)->format('g:i A') }}
                                @if($segment->code)
                                    • {{ $segment->code }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right text-sm">
                            @if($segment->client)
                                <div>Client: {{ $segment->client->name }}</div>
                            @endif
                            @if($segment->producer)
                                <div>Producer: {{ $segment->producer->name }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cues in Segment -->
                <div class="p-4 space-y-2">
                    @forelse($segmentCues as $cue)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4
                            @if($standbyCueId === $cue->id) ring-2 ring-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 @endif
                            @if($cue->status === 'complete') bg-green-50 dark:bg-green-900/20 @endif
                            @if($cue->status === 'skip') bg-gray-100 dark:bg-gray-700 opacity-60 @endif
                        ">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-lg font-mono font-bold text-gray-900 dark:text-gray-100">
                                            {{ \Carbon\Carbon::parse($cue->time)->format('g:i A') }}
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $cue->cue_number }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $cue->cueType->color }}20; color: {{ $cue->cueType->color }}">
                                            {{ $cue->cueType->name }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cue->status_badge_color }}">
                                            {{ ucfirst($cue->status) }}
                                        </span>
                                        @if($cue->priority !== 'normal')                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($cue->priority === 'high') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif
                                                @if($cue->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif
                                            ">
                                                {{ ucfirst($cue->priority) }} Priority
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-base font-medium text-gray-900 dark:text-gray-100 mb-1">
                                        {{ $cue->name }}
                                    </div>
                                    @if($cue->description)
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            {{ $cue->description }}
                                        </div>
                                    @endif
                                    @if($cue->filename)
                                        <div class="text-sm text-blue-600 dark:text-blue-400 mb-2">
                                            📁 {{ $cue->filename }}
                                        </div>
                                    @endif
                                    @if($cue->operator)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Operator: {{ $cue->operator->name }}
                                        </div>
                                    @endif
                                    @if($cue->notes)
                                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 italic">
                                            Note: {{ $cue->notes }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 ml-4">
                                    @if($cue->status === 'pending')
                                        <flux:button wire:click="setStandby({{ $cue->id }})" variant="primary">
                                            Standby
                                        </flux:button>
                                    @endif
                                    @if($standbyCueId === $cue->id)
                                        <flux:button wire:click="executeCue({{ $cue->id }})" variant="primary" class="text-lg font-bold">
                                            GO
                                        </flux:button>
                                        <flux:button wire:click="skipCue({{ $cue->id }})" variant="ghost">
                                            Skip
                                        </flux:button>
                                    @endif
                                    @if($cue->status !== 'pending')
                                        <flux:button wire:click="resetCue({{ $cue->id }})" variant="ghost" size="sm">
                                            Reset
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                            No cues in this segment
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    @endforeach
</div>
