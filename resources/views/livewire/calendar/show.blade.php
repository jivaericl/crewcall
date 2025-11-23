<div class="p-6">
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-start gap-4">
            <span class="w-4 h-4 rounded-full mt-1 flex-shrink-0" style="background-color: {{ $calendarItem->type_color }};"></span>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $calendarItem->title }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                        {{ $calendarItem->type === 'milestone' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                        {{ $calendarItem->type === 'out_of_office' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' : '' }}
                        {{ $calendarItem->type === 'call' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                        {{ $calendarItem->type_label }}
                    </span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $calendarItem->formatted_date_range }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('events.calendar.edit', [$eventId, $calendarItem->id]) }}" 
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            <button 
                wire:click="delete" 
                wire:confirm="Are you sure you want to delete this calendar item?"
                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Description --}}
            @if($calendarItem->description)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($calendarItem->description)) !!}
                    </div>
                </div>
            @endif

            {{-- Attendees --}}
            @if($calendarItem->users->count() > 0 || $calendarItem->speakers->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attendees</h2>
                    
                    {{-- Team Members --}}
                    @if($calendarItem->users->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team Members</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($calendarItem->users as $user)
                                    <a href="{{ route('events.team-members.show', [$eventId, $user->id]) }}" 
                                       class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                                        <span class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-xs font-medium mr-2">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                        {{ $user->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Speakers --}}
                    @if($calendarItem->speakers->count() > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Speakers</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($calendarItem->speakers as $speaker)
                                    <a href="{{ route('events.speakers.show', [$eventId, $speaker->id]) }}" 
                                       class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 hover:bg-purple-200 dark:hover:bg-purple-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $speaker->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Tags --}}
            @if($calendarItem->tags->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Tags</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($calendarItem->tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Details Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Details</h2>
                
                <dl class="space-y-3">
                    {{-- Date & Time --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $calendarItem->formatted_date_range }}
                            @if($calendarItem->all_day)
                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">(All day)</span>
                            @endif
                        </dd>
                    </div>

                    {{-- Location --}}
                    @if($calendarItem->location)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $calendarItem->location }}
                                </div>
                            </dd>
                        </div>
                    @endif

                    {{-- Color --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Color</dt>
                        <dd class="mt-1 flex items-center gap-2">
                            <span class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" style="background-color: {{ $calendarItem->color }};"></span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $calendarItem->color }}</span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Metadata Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h2>
                
                <dl class="space-y-3">
                    {{-- Created By --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $calendarItem->creator->name ?? 'Unknown' }}
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $calendarItem->created_at->format('M d, Y g:i A') }}
                            </div>
                        </dd>
                    </div>

                    {{-- Last Updated --}}
                    @if($calendarItem->updated_at != $calendarItem->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $calendarItem->updater->name ?? 'Unknown' }}
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $calendarItem->updated_at->format('M d, Y g:i A') }}
                                </div>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Back to Calendar --}}
            <a href="{{ route('events.calendar.index', $eventId) }}" 
               class="block w-full text-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                ← Back to Calendar
            </a>
        </div>
    </div>
</div>
