<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                View Cue: {{ $cue->name }}
            </h2>
            <a href="{{ route('segments.cues.edit', ['segmentId' => $segmentId, 'cueId' => $cueId]) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Cue
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Cue Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cue Name</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cue Code</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->code ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cue Type</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->cueType->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->time ? $cue->time->format('H:i') : 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $cue->statusBadgeColor }}-100 text-{{ $cue->statusBadgeColor }}-800 dark:bg-{{ $cue->statusBadgeColor }}-900 dark:text-{{ $cue->statusBadgeColor }}-200">
                                    {{ ucfirst($cue->status) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $cue->priorityBadgeColor }}-100 text-{{ $cue->priorityBadgeColor }}-800 dark:bg-{{ $cue->priorityBadgeColor }}-900 dark:text-{{ $cue->priorityBadgeColor }}-200">
                                    {{ ucfirst($cue->priority) }}
                                </span>
                            </div>

                            @if($cue->operator)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operator</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->operator->name }}</p>
                            </div>
                            @endif

                            @if($cue->filename)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filename</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->filename }}</p>
                            </div>
                            @endif
                        </div>

                        @if($cue->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $cue->description }}</p>
                        </div>
                        @endif

                        @if($cue->notes)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $cue->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Custom Fields -->
                    @if($cue->customFieldValues->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Custom Fields</h3>
                        <div class="space-y-4">
                            @foreach($cue->customFieldValues as $fieldValue)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $fieldValue->customField->label }}
                                    </label>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $fieldValue->value ?? 'N/A' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- History (Collapsible) -->
                    <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <button @click="open = !open" type="button" class="w-full px-6 py-4 flex justify-between items-center text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $log->action }}
                                                        </p>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                            by {{ $log->user->name ?? 'System' }} • {{ $log->created_at->format('M d, Y g:i A') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if($log->changes)
                                                    <div class="mt-2 text-sm">
                                                        @foreach($log->changes as $field => $change)
                                                            <div class="mt-1">
                                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                                @if(isset($change['old']) && isset($change['new']))
                                                                    <span class="text-red-600 dark:text-red-400">{{ $change['old'] ?? 'null' }}</span>
                                                                    →
                                                                    <span class="text-green-600 dark:text-green-400">{{ $change['new'] ?? 'null' }}</span>
                                                                @else
                                                                    <span class="text-gray-600 dark:text-gray-400">{{ json_encode($change) }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
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
                        @if($cue->comments->count() > 0)
                            <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                @foreach($cue->comments as $comment)
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
                        <div class="space-y-3 text-sm">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Event</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->segment->session->event->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Session</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->segment->session->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Segment</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->segment->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tags</h3>
                        @if($cue->tags->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($cue->tags as $tag)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white" style="background-color: {{ $tag->color }}">
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
                        <div class="space-y-3 text-sm">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Created By</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->creator->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cue->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Last Updated By</label>
                                <p class="text-gray-900 dark:text-gray-100">{{ $cue->updater->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cue->updated_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
