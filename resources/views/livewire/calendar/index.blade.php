<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendar & Schedule</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage milestones, out of office dates, and calls</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('events.calendar.view', $eventId) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Calendar View
            </a>
            <a href="{{ route('events.calendar.create', $eventId) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Calendar Item
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    {{-- Type Toggles --}}
    <div class="mb-6 flex flex-wrap gap-3">
        <button 
            wire:click="toggleType('milestone')"
            class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showMilestones ? 'bg-green-50 dark:bg-green-900/20 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #10B981;"></span>
            <span class="font-medium">Production Milestones</span>
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $showMilestones ? 'bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                {{ $milestonesCount }}
            </span>
        </button>

        <button 
            wire:click="toggleType('out_of_office')"
            class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showOutOfOffice ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-500 dark:border-amber-600 text-amber-700 dark:text-amber-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #F59E0B;"></span>
            <span class="font-medium">Out of Office</span>
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $showOutOfOffice ? 'bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-200' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                {{ $outOfOfficeCount }}
            </span>
        </button>

        <button 
            wire:click="toggleType('call')"
            class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showCalls ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500 dark:border-blue-600 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #3B82F6;"></span>
            <span class="font-medium">Calls</span>
            <span class="ml-2 px-2 py-0.5 text-xs rounded-full {{ $showCalls ? 'bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                {{ $callsCount }}
            </span>
        </button>
    </div>

    {{-- Search and Filters --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input 
                    type="text" 
                    id="search"
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search title, description, location..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
            </div>

            <div>
                <label for="typeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Type</label>
                <select 
                    id="typeFilter"
                    wire:model.live="typeFilter"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="milestone">Production Milestones</option>
                    <option value="out_of_office">Out of Office</option>
                    <option value="call">Calls</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input 
                        type="date" 
                        id="startDate"
                        wire:model.live="startDate"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input 
                        type="date" 
                        id="endDate"
                        wire:model.live="endDate"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar Items List --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @if($calendarItems->count() > 0)
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button wire:click="changeSortField('title')" class="flex items-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200">
                                Title
                                @if($sortBy === 'title')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button wire:click="changeSortField('type')" class="flex items-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200">
                                Type
                                @if($sortBy === 'type')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button wire:click="changeSortField('start_date')" class="flex items-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:text-gray-700 dark:hover:text-gray-200">
                                Date/Time
                                @if($sortBy === 'start_date')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Attendees
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($calendarItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $item->type_color }};"></span>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->title }}
                                        </div>
                                        @if($item->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-md">
                                                {{ Str::limit($item->description, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->type === 'milestone' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $item->type === 'out_of_office' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' : '' }}
                                    {{ $item->type === 'call' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                    {{ $item->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $item->formatted_date_range }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->location ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($item->users->count() > 0)
                                    <div class="flex -space-x-2">
                                        @foreach($item->users->take(3) as $user)
                                            <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xs font-medium text-gray-700 dark:text-gray-200 border-2 border-white dark:border-gray-800" title="{{ $user->name }}">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($item->users->count() > 3)
                                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300 border-2 border-white dark:border-gray-800">
                                                +{{ $item->users->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('events.calendar.show', [$eventId, $item->id]) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                       title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('events.calendar.edit', [$eventId, $item->id]) }}" 
                                       class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button 
                                        wire:click="delete({{ $item->id }})" 
                                        wire:confirm="Are you sure you want to delete this calendar item?"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                        title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $calendarItems->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No calendar items</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new calendar item.</p>
                <div class="mt-6">
                    <a href="{{ route('events.calendar.create', $eventId) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Calendar Item
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
