<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
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
                <button 
                    wire:click="openResetModal" 
                    class="px-4 py-2 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-medium rounded-md"
                    type="button">
                    Reset All Cues
                </button>
                <flux:button href="{{ route('events.sessions.edit', [$eventId, $sessionId]) }}" variant="ghost">
                    Edit Session
                </flux:button>
                <flux:button href="{{ route('events.sessions.index', $eventId) }}" variant="ghost">
                    Back to Sessions
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            <!-- Session Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
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

                    <!-- Comments -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Comments</h3>
                        
                        <!-- Success Message -->
                        @if (session()->has('message'))
                            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                                {{ session('message') }}
                            </div>
                        @endif

                        <!-- Comment Form -->
                        <form wire:submit.prevent="postComment" class="mb-6">
                            <div>
                                <label for="newComment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add a comment</label>
                                <textarea 
                                    wire:model="newComment" 
                                    id="newComment" 
                                    rows="3" 
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Write your comment here..."
                                ></textarea>
                                @error('newComment') 
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                >
                                    Post Comment
                                </button>
                            </div>
                        </form>

                        <!-- Existing Comments -->
                        @if($session->comments->count() > 0)
                            <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                @foreach($session->comments as $comment)
                                    <div class="border-l-4 border-gray-300 dark:border-gray-600 pl-4 py-2">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-300 font-medium text-sm">
                                                    {{ substr($comment->user->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm border-t border-gray-200 dark:border-gray-700 pt-4">No comments yet. Be the first to comment!</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Tags -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tags</h3>
                        @if($session->tags->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($session->tags as $tag)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No tags assigned</p>
                        @endif
                    </div>

                    <!-- Metadata -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Metadata</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $session->creator?->name ?: 'Unknown' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $session->created_at->format('M d, Y g:i A') }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $session->updater?->name ?: 'Unknown' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $session->updated_at->format('M d, Y g:i A') }}</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- History (Collapsible) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full p-6 text-left flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">History</h3>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                @if($auditLogs->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($auditLogs as $log)
                                            <div class="border-l-4 border-blue-500 dark:border-blue-400 pl-4 py-2">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ ucfirst($log->action) }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            by {{ $log->user->name }} • {{ $log->created_at->diffForHumans() }}
                                                        </p>
                                                        @if($log->changes && count($log->changes) > 0)
                                                            <div class="mt-2 space-y-1">
                                                                @foreach($log->changes as $field => $change)
                                                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                                        @if(isset($change['old']) && isset($change['new']))
                                                                            <span class="text-red-600 dark:text-red-400">{{ $change['old'] ?: '(empty)' }}</span>
                                                                            →
                                                                            <span class="text-green-600 dark:text-green-400">{{ $change['new'] ?: '(empty)' }}</span>
                                                                        @else
                                                                            {{ json_encode($change) }}
                                                                        @endif
                                                                    </p>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No history available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Reset Confirmation Modal -->
    <div x-data="{ show: @entangle('showResetModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="show = false"></div>
        
        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                 @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Reset All Cues
                </h3>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    This action will set all cues in this session back to the default state. 
                    To confirm, type <strong>RESET</strong> in the box below.
                </p>
                
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                <input 
                    type="text" 
                    wire:model="resetConfirmation" 
                    placeholder="Type RESET to confirm"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-4"
                />
                
                <div class="flex gap-2 justify-end">
                    <button 
                        wire:click="closeResetModal" 
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium"
                        type="button">
                        Cancel
                    </button>
                    <button 
                        wire:click="resetAllCues" 
                        class="px-4 py-2 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white rounded-md text-sm font-medium"
                        type="button">
                        Reset All Cues
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>