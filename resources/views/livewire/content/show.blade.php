<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $content->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Content Details - {{ $event->name }}
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('events.content.index', $eventId) }}" variant="ghost">
                    ← Back to Content
                </flux:button>
                <flux:button href="{{ route('events.content.edit', ['eventId' => $eventId, 'contentId' => $contentId]) }}" variant="primary">
                    Edit
                </flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">{{ $content->file_type_icon }}</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">File Type</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst($content->file_type) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Version</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">v{{ $content->current_version }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ method_exists($content, 'tags') && $content->tags ? $content->tags->count() : 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">References</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $content->speakers->count() + $content->segments->count() + $content->cues->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- File Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">File Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->formatted_file_size }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">MIME Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->mime_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                    <dd class="mt-1">
                                        @if($content->category)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $content->category->color }}20; color: {{ $content->category->color }}">
                                                {{ $content->category->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">No category</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->creator->name ?? 'Unknown' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->created_at->format('M d, Y g:i A') }}</dd>
                                </div>
                                @if($content->updated_at != $content->created_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->updater->name ?? 'Unknown' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated At</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $content->updated_at->format('M d, Y g:i A') }}</dd>
                                    </div>
                                @endif
                            </dl>

                            @if($content->description)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $content->description }}</dd>
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <flux:button href="{{ $content->download_url }}" target="_blank" variant="primary">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download File
                                </flux:button>
                            </div>
                        </div>
                    </div>

                    <!-- Version History -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Version History</h3>
                        </div>
                        <div class="p-6">
                            @if($content->versions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($content->versions as $version)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ $version->version_number == $content->current_version ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-700' : '' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            v{{ $version->version_number }}
                                                        </span>
                                                        @if($version->version_number == $content->current_version)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Current
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-900 dark:text-gray-100 mt-2">
                                                        <strong>Size:</strong> {{ number_format($version->file_size / 1024, 2) }} KB
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        <strong>Uploaded:</strong> {{ $version->created_at->format('M d, Y g:i A') }} by {{ $version->uploader->name ?? 'Unknown' }}
                                                    </p>
                                                    @if($version->change_notes)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            <strong>Notes:</strong> {{ $version->change_notes }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <flux:button href="{{ \Illuminate\Support\Facades\Storage::url($version->file_path) }}" target="_blank" variant="ghost" size="sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                    </flux:button>
                                                </div>
                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No version history available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="space-y-6">
                    <!-- Tags -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tags</h3>
                        </div>
                        <div class="p-6">
                            @if(method_exists($content, 'tags') && $content->tags && $content->tags->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($content->tags as $tag)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tags feature not yet implemented</p>
                            @endif
                        </div>
                    </div>

                    <!-- Assignments & References -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Assignments</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Speakers -->
                            @if($content->speakers->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Speakers ({{ $content->speakers->count() }})</h4>
                                    <ul class="space-y-1">
                                        @foreach($content->speakers as $speaker)
                                            <li class="text-sm text-gray-600 dark:text-gray-400">
                                                <a href="{{ route('events.speakers.show', ['eventId' => $eventId, 'speakerId' => $speaker->id]) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $speaker->first_name }} {{ $speaker->last_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Sessions (via Segments) -->
                            @if($content->segments->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sessions ({{ $content->segments->pluck('session')->unique('id')->count() }})</h4>
                                    <ul class="space-y-1">
                                        @foreach($content->segments->pluck('session')->unique('id') as $session)
                                            <li class="text-sm text-gray-600 dark:text-gray-400">
                                                <a href="{{ route('events.sessions.show', ['eventId' => $eventId, 'sessionId' => $session->id]) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $session->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Segments -->
                            @if($content->segments->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Segments ({{ $content->segments->count() }})</h4>
                                    <ul class="space-y-1">
                                        @foreach($content->segments as $segment)
                                            <li class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $segment->name }}
                                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $segment->session->name }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Cues -->
                            @if($content->cues->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cues ({{ $content->cues->count() }})</h4>
                                    <ul class="space-y-1">
                                        @foreach($content->cues as $cue)
                                            <li class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $cue->name }}
                                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $cue->segment->session->name }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($content->speakers->count() == 0 && $content->segments->count() == 0 && $content->cues->count() == 0)
                                <p class="text-sm text-gray-500 dark:text-gray-400">Not assigned to any speakers, sessions, or cues</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
