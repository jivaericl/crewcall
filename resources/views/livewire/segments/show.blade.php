<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                View Segment: {{ $segment->name }}
            </h2>
            <a href="{{ route('sessions.segments.edit', [$sessionId, $segmentId]) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Segment
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Segment Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Segment Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Code</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->code ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->start_time ? $segment->start_time->format('g:i A') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">End Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->end_time ? $segment->end_time->format('g:i A') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->duration ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Producer</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->producer?->name ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->client?->name ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sort Order</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->sort_order }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Custom Fields -->
                    @if($segment->customFieldValues->count() > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Custom Fields</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                @foreach($segment->customFieldValues as $fieldValue)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $fieldValue->customField->name }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $fieldValue->value ?: '-' }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    <!-- Cues -->
                    @if($segment->cues->count() > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cues ({{ $segment->cues->count() }})</h3>
                            <div class="space-y-2">
                                @foreach($segment->cues as $cue)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cue->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cue->code }} • {{ $cue->type }}</p>
                                        </div>
                                        <a href="{{ route('segments.cues.show', [$segmentId, $cue->id]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                            View →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

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
                        @if($segment->comments->count() > 0)
                            <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                @foreach($segment->comments as $comment)
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

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Context -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Context</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Event</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->session->event->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Session</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $segment->session->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tags</h3>
                        @if($segment->tags->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($segment->tags as $tag)
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
                                    {{ $segment->creator?->name ?: 'Unknown' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $segment->created_at->format('M d, Y g:i A') }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $segment->updater?->name ?: 'Unknown' }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $segment->updated_at->format('M d, Y g:i A') }}</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
