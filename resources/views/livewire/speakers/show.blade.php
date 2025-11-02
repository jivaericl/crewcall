<div>
    <flux:header container class="mb-6">
        <flux:heading size="xl">{{ $speaker->name }}</flux:heading>
        
        <flux:button href="{{ route('events.speakers.edit', ['eventId' => $eventId, 'speakerId' => $speakerId]) }}" icon="pencil">
            Edit Speaker
        </flux:button>
    </flux:header>

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
                                <h4 class="font-semibold">{{ $session->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $session->start_time }} - {{ $session->end_time }}
                                </p>
                                @if($session->location)
                                    <p class="text-sm text-gray-500">{{ $session->location }}</p>
                                @endif
                            </div>
                            <flux:button size="sm" href="{{ route('sessions.edit', ['eventId' => $eventId, 'sessionId' => $session->id]) }}" variant="ghost">
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
                                <h4 class="font-semibold">{{ $file->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ strtoupper($file->file_type) }} • {{ $file->formatted_size }}
                                </p>
                            </div>
                            <flux:button size="sm" href="{{ $file->download_url }}" variant="ghost" target="_blank">
                                Download
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No content files assigned.</p>
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

            <flux:card>
                <flux:heading size="lg" class="mb-4">Comments</flux:heading>
                
                @livewire('comments.comment-section', [
                    'commentableType' => get_class($speaker),
                    'commentableId' => $speaker->id,
                    'eventId' => $eventId
                ])
            </flux:card>
        </div>
    </div>
</div>
