<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">All Segments</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                View all segments across sessions for {{ $event->name }}
            </p>
        </div>

        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search sessions or segments..." 
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
            />
        </div>

        <!-- Sessions List -->
        <div class="space-y-4">
            @forelse($sessions as $session)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden" x-data="{ sessionOpen: false }">
                    <!-- Session Header (Collapsible) -->
                    <div 
                        @click="sessionOpen = !sessionOpen" 
                        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <svg 
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform" 
                                :class="{ 'rotate-90': sessionOpen }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $session->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $session->start_date->format('M d, Y g:i A') }} | 
                                    {{ $session->segments->count() }} segments
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a 
                                href="{{ route('sessions.segments.index', $session->id) }}" 
                                class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800"
                                onclick="event.stopPropagation()"
                            >
                                View Session
                            </a>
                        </div>
                    </div>

                    <!-- Segments Table (Collapsible) -->
                    <div x-show="sessionOpen" x-collapse class="border-t border-gray-200 dark:border-gray-700">
                        @if($session->segments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cues</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($session->segments as $segment)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-lg">📋</span>
                                                        <span>{{ $segment->sort_order }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $segment->name }}</div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if($segment->description)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($segment->description, 100) }}</div>
                                                    @else
                                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    @if($segment->duration)
                                                        {{ $segment->duration }} min
                                                    @else
                                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                        {{ $segment->cues->count() }} cues
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a 
                                                            href="{{ route('sessions.segments.show', ['sessionId' => $session->id, 'segmentId' => $segment->id]) }}" 
                                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                                        >
                                                            View
                                                        </a>
                                                        <a 
                                                            href="{{ route('segments.cues.index', $segment->id) }}" 
                                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                        >
                                                            Cues
                                                        </a>
                                                        <a 
                                                            href="{{ route('sessions.segments.edit', ['sessionId' => $session->id, 'segmentId' => $segment->id]) }}" 
                                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                                        >
                                                            Edit
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No segments in this session
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No sessions found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($search)
                            No sessions or segments match your search.
                        @else
                            Get started by creating a session.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
