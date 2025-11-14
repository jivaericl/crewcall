<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
    <div class="mb-4 flex gap-4">
        <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Segment:</label>
            <select wire:model.live="filterSegment" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">All Segments</option>
                @foreach($segments as $segment)
                    <option value="{{ $segment->id }}">{{ $segment->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Cue Type:</label>
            <select wire:model.live="filterCueType" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">All Types</option>
                <option value="audio">Audio</option>
                <option value="video">Video</option>
                <option value="lighting">Lighting</option>
                <option value="other">Other</option>
            </select>
        </div>
    </div>

    @if($segments->count() > 0)
        <div class="space-y-6">
            @foreach($segments as $segment)
                @php
                    $segmentCues = $cues->where('segment_id', $segment->id);
                    if ($filterCueType) {
                        $segmentCues = $segmentCues->where('type', $filterCueType);
                    }
                @endphp
                
                @if($segmentCues->count() > 0)
                    <div class="border-l-4 border-blue-500 dark:border-blue-400 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            {{ $segment->name }}
                        </h3>
                        
                        <div class="space-y-3">
                            @foreach($segmentCues as $cue)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="text-sm font-mono font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $cue->time }}
                                                </span>
                                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    Cue {{ $cue->cue_number }}
                                                </span>
                                                <span class="text-xs px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                                    {{ ucfirst($cue->type) }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                                {{ $cue->description }}
                                            </p>
                                            
                                            <div class="flex gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span>Operator: {{ $cue->operator ?? 'Unassigned' }}</span>
                                                <span>Status: 
                                                    <span class="font-medium {{ $cue->status === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                                        {{ ucfirst($cue->status ?? 'pending') }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex gap-2 ml-4">
                                            <flux:button wire:click="standby({{ $cue->id }})" variant="ghost" size="sm">
                                                Standby
                                            </flux:button>
                                            <button 
                                                wire:click="go({{ $cue->id }})" 
                                                class="px-3 py-1.5 text-sm font-medium rounded-md bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-600 dark:text-white dark:hover:bg-emerald-500 transition-colors">
                                                GO
                                            </button>
                                            <flux:button wire:click="skip({{ $cue->id }})" variant="ghost" size="sm">
                                                Skip
                                            </flux:button>
                                            <flux:button wire:click="resetCueStatus({{ $cue->id }})" variant="ghost" size="sm">
                                                Reset
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 dark:text-gray-400">No segments found for this session.</p>
        </div>
    @endif
</div>
