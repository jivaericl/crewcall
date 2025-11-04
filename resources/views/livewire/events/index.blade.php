<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Events') }}
            </h2>
            <flux:button href="{{ route('events.create') }}" variant="primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Event
            </flux:button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Events</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition" wire:click="$set('filterStatus', 'upcoming')">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Upcoming</dt>
                                    <dd class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $stats['upcoming'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition" wire:click="$set('filterStatus', 'ongoing')">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Ongoing</dt>
                                    <dd class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $stats['ongoing'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition" wire:click="$set('filterStatus', 'past')">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Past</dt>
                                    <dd class="text-lg font-semibold text-gray-600 dark:text-gray-400">{{ $stats['past'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <flux:input 
                                    wire:model.live.debounce.300ms="search" 
                                    type="text" 
                                    placeholder="Search events by name or description..."
                                    class="w-full"
                                >
                                    <x-slot name="iconLeading">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </x-slot>
                                </flux:input>
                            </div>

                            <!-- Tag Filter -->
                            <div>
                                <flux:select wire:model.live="filterTag">
                                    <option value="">All Tags</option>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </flux:select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <flux:select wire:model.live="filterStatus">
                                    <option value="all">All Status</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="past">Past</option>
                                </flux:select>
                            </div>
                        </div>

                        <!-- Active Filters & Clear -->
                        @if($search || $filterTag || $filterStatus !== 'all')
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>
                                @if($search)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Search: "{{ $search }}"
                                    </span>
                                @endif
                                @if($filterTag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        Tag: {{ $tags->find($filterTag)->name }}
                                    </span>
                                @endif
                                @if($filterStatus !== 'all')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Status: {{ ucfirst($filterStatus) }}
                                    </span>
                                @endif
                                <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                                    Clear All
                                </flux:button>
                            </div>
                        @endif
                    </div>

                    <!-- Events Table -->
                    @if($events->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th wire:click="sortByColumn('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                                            <div class="flex items-center">
                                                Event Name
                                                @if($sortBy === 'name')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        @if($sortDirection === 'asc')
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        @else
                                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                        @endif
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>
                                        <th wire:click="sortByColumn('start_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                                            <div class="flex items-center">
                                                Start Date
                                                @if($sortBy === 'start_date')
                                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                        @if($sortDirection === 'asc')
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        @else
                                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                        @endif
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tags</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Updated</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($events as $event)
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $status = 'upcoming';
                                            $statusColor = 'blue';
                                            if ($event->start_date <= $now && $event->end_date >= $now) {
                                                $status = 'ongoing';
                                                $statusColor = 'green';
                                            } elseif ($event->end_date < $now) {
                                                $status = 'past';
                                                $statusColor = 'gray';
                                            }
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $event->name }}
                                                </div>
                                                @if($event->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md">
                                                        {{ Str::limit($event->description, 60) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $event->start_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $event->start_date->format('g:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse($event->tags as $tag)
                                                        <flux:badge size="sm" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                                                            {{ $tag->name }}
                                                        </flux:badge>
                                                    @empty
                                                        <span class="text-xs text-gray-400">No tags</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($statusColor === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @elseif($statusColor === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                    @endif">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $event->updated_at->diffForHumans() }}
                                                @if($event->updater)
                                                    <div class="text-xs">by {{ $event->updater->name }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-1">
                                                    <flux:button href="{{ route('events.sessions.index', $event->id) }}" variant="ghost" size="sm" title="Sessions">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button href="{{ route('events.content.index', $event->id) }}" variant="ghost" size="sm" title="Content Library">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button href="{{ route('events.speakers.index', $event->id) }}" variant="ghost" size="sm" title="Speakers">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button href="{{ route('show-call.index', $event->id) }}" variant="primary" size="sm" title="Show Calling">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button href="{{ route('events.users', $event->id) }}" variant="ghost" size="sm" title="Manage Users">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button href="{{ route('events.edit', $event->id) }}" variant="ghost" size="sm" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button wire:click="duplicateEvent({{ $event->id }})" variant="ghost" size="sm" title="Duplicate">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button wire:click="confirmDelete({{ $event->id }})" variant="danger" size="sm" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </flux:button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $events->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No events found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($search || $filterTag || $filterStatus !== 'all')
                                    Try adjusting your filters or search terms.
                                @else
                                    Get started by creating your first event.
                                @endif
                            </p>
                            <div class="mt-6">
                                @if($search || $filterTag || $filterStatus !== 'all')
                                    <flux:button wire:click="clearFilters" variant="ghost">
                                        Clear Filters
                                    </flux:button>
                                @else
                                    <flux:button href="{{ route('events.create') }}" variant="primary">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Create Your First Event
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
        <flux:modal wire:model="showDeleteModal">
            <flux:modal.content>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Event</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this event? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <flux:button wire:click="cancelDelete" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button wire:click="deleteEvent" variant="danger">
                            Delete Event
                        </flux:button>
                    </div>
                </div>
            </flux:modal.content>
        </flux:modal>
    @endif
</div>
