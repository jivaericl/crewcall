<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $speaker->name }}</h2>
        </div>
        <div><flux:button href="{{ route('events.speakers.edit', ['eventId' => $eventId, 'speakerId' => $speakerId]) }}" icon="pencil">
            Edit Speaker
        </flux:button></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <flux:card>
                <div class="text-center">
                    @if($speaker->headshot_url)
                        <img src="{{ $speaker->headshot_url }}" alt="{{ $speaker->name }}" class="w-48 h-48 rounded-full object-cover mx-auto mb-4">
                    @else
                        <div class="w-48 h-48 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <span class="text-6xl text-gray-500 dark:text-gray-400 font-semibold">{{ substr($speaker->name, 0, 1) }}</span>
                        </div>
                    @endif

                    <flux:heading size="lg">{{ $speaker->name }}</flux:heading>
                    @if($speaker->full_title)
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $speaker->full_title }}</p>
                    @endif

                    @if($speaker->email)
                        <p class="text-sm text-gray-500 mt-2">{{ $speaker->email }}</p>
                    @endif

                    @if($speaker->contact_person)
                        <p class="text-sm text-gray-500 mt-1">Contact: {{ $speaker->contact_person }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2 justify-center mt-4">
                        @if(!$speaker->is_active)
                            <flux:badge color="gray">Inactive</flux:badge>
                        @endif
                        @if($speaker->user_id)
                            <flux:badge color="blue">Has Account</flux:badge>
                        @endif
                    </div>

                    @if($speaker->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 justify-center mt-4">
                            @foreach($speaker->tags as $tag)
                                <flux:badge :style="'background-color: ' . $tag->color . '; color: white;'">
                                    {{ $tag->name }}
                                </flux:badge>
                            @endforeach
                        </div>
                    @endif
                </div>
            </flux:card>

            @if($speaker->bio)
                <flux:card class="mt-6">
                    <flux:heading size="md" class="mb-2">Biography</flux:heading>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $speaker->bio }}</p>
                </flux:card>
            @endif

            @if($speaker->notes)
                <flux:card class="mt-6">
                    <flux:heading size="md" class="mb-2">Internal Notes</flux:heading>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $speaker->notes }}</p>
                </flux:card>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Sessions ({{ $speaker->sessions->count() }})</flux:heading>
                
                @forelse($speaker->sessions as $session)
                    <div class="border-b border-gray-200 dark:border-gray-700 py-3 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $session->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $session->start_date->format('M d, Y g:i A') }} - {{ $session->end_date->format('g:i A') }}
                                </p>
                                @if($session->location)
                                    <p class="text-sm text-gray-500">{{ $session->location }}</p>
                                @endif
                            </div>
                            <flux:button size="sm" href="{{ route('events.sessions.show', ['eventId' => $eventId, 'sessionId' => $session->id]) }}" variant="ghost">
                                View
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No sessions assigned.</p>
                @endforelse
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Content Files ({{ $speaker->contentFiles->count() }})</flux:heading>
                
                @forelse($speaker->contentFiles as $file)
                    <div class="border-b border-gray-200 dark:border-gray-700 py-3 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $file->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ strtoupper($file->file_type) }} • {{ $file->formatted_size }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <flux:button size="sm" href="{{ route('events.content.show', ['eventId' => $eventId, 'contentId' => $file->id]) }}" variant="ghost">
                                    View
                                </flux:button>
                                <flux:button size="sm" href="{{ $file->download_url }}" variant="ghost" target="_blank">
                                    Download
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No content files assigned.</p>
                @endforelse
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Activity</flux:heading>
                
                <div class="space-y-3">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Created:</strong> {{ $speaker->created_at->format('M d, Y g:i A') }}
                        @if($speaker->creator)
                            by {{ $speaker->creator->name }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Last Updated:</strong> {{ $speaker->updated_at->format('M d, Y g:i A') }}
                        @if($speaker->updater)
                            by {{ $speaker->updater->name }}
                        @endif
                    </div>
                </div>
            </flux:card>

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

            <flux:card>
                <flux:heading size="lg" class="mb-4">Comments</flux:heading>
                
                @livewire('comments.comment-section', [
                    'commentable' => $speaker,
                    'eventId' => $eventId
                ])
            </flux:card>
        </div>
    </div>
    </div>
</div>
