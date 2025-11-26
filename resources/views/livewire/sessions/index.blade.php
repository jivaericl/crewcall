<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sessions for {{ $event->name }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('custom-fields.index', $eventId) }}" variant="ghost">
                    Manage Custom Fields
                </flux:button>
                <flux:button href="{{ route('events.index') }}" variant="ghost">
                    Back to Events
                </flux:button>
                <a href="{{ route('events.sessions.create', $eventId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Session
                </a>
            </div>
        </div>

        <div>
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Search -->
                    <div class="mb-6">
                        <flux:input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search sessions by name, code, description, or location..."
                            class="w-full"
                        >
                            <x-slot name="iconLeading">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </x-slot>
                        </flux:input>
                    </div>

                    @if($sessions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Session</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Producer</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($sessions as $session)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $session->start_date->format('M d') }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $session->start_date->format('g:i A') }} - {{ $session->end_date->format('g:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $session->name }}
                                                    @if($session->code)
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            {{ $session->code }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($session->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md">
                                                        {{ Str::limit($session->description, 60) }}
                                                    </div>
                                                @endif
                                                @if($session->tags->count() > 0)
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach($session->tags as $tag)
                                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                                {{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $session->location ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $session->client?->name ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $session->producer?->name ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-1">
                                                    <flux:button href="{{ route('events.sessions.show', [$eventId, $session->id]) }}" variant="ghost" size="sm" title="View">
                                                        <x-action-icon action="view" />
                                                    </flux:button>
                                                    <a href="{{ route('sessions.run-of-show', $session->id) }}" title="Run of Show" class="inline-flex items-center justify-center px-2 py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition ease-in-out duration-150">
                                                        <x-action-icon action="run-of-show" />
                                                    </a>
                                                    <flux:button href="{{ route('sessions.segments.index', $session->id) }}" variant="ghost" size="sm" title="Segments">
                                                        <x-action-icon action="segments" />
                                                    </flux:button>
                                                    <flux:button href="{{ route('events.sessions.edit', [$eventId, $session->id]) }}" variant="ghost" size="sm" title="Edit">
                                                        <x-action-icon action="edit" />
                                                    </flux:button>
                                                    <flux:button wire:click="duplicateSession({{ $session->id }})" variant="ghost" size="sm" title="Duplicate">
                                                        <x-action-icon action="duplicate" />
                                                    </flux:button>
                                                    <flux:button wire:click="confirmDelete({{ $session->id }})" variant="danger" size="sm" title="Delete">
                                                        <x-action-icon action="delete" />
                                                    </flux:button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No sessions found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($search)
                                    Try adjusting your search terms.
                                @else
                                    Get started by creating your first session.
                                @endif
                            </p>
                            <div class="mt-6">
                                @if($search)
                                    <flux:button wire:click="$set('search', '')" variant="ghost">
                                        Clear Search
                                    </flux:button>
                                @else
                                    <flux:button href="{{ route('events.sessions.create', $eventId) }}" variant="primary">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create Your First Session
                                    </flux:button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <flux:modal wire:model.live="showDeleteModal">
            <flux:modal.content>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Session</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this session? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <flux:button wire:click="cancelDelete" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button wire:click="deleteSession" variant="danger">
                            Delete Session
                        </flux:button>
                    </div>
                </div>
            </flux:modal.content>
        </flux:modal>
    @endif
    </div>
</div>
