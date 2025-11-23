<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $calendarItemId ? 'Edit Calendar Item' : 'Create Calendar Item' }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $calendarItemId ? 'Update the calendar item details below' : 'Add a new milestone, out of office date, or call to your calendar' }}
        </p>
    </div>

    <form wire:submit="save">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
            
            {{-- Type Selection --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Type <span class="text-red-500">*</span>
                </label>
                <select 
                    id="type"
                    wire:model.live="type"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="milestone">🟢 Production Milestone</option>
                    <option value="out_of_office">🟠 Out of Office</option>
                    <option value="call">🔵 Call</option>
                </select>
                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title"
                    wire:model="title"
                    placeholder="Enter a descriptive title..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea 
                    id="description"
                    wire:model="description"
                    rows="4"
                    placeholder="Add additional details..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Date and Time --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Start Date/Time --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="start_date"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Time
                        </label>
                        <input 
                            type="time" 
                            wire:model="start_time"
                            @if($all_day) disabled @endif
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        />
                        @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- End Date/Time --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="end_date"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Time
                        </label>
                        <input 
                            type="time" 
                            wire:model="end_time"
                            @if($all_day) disabled @endif
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        />
                        @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- All Day Checkbox --}}
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="all_day"
                    wire:model="all_day"
                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                <label for="all_day" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    All day event
                </label>
            </div>

            {{-- Location --}}
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Location
                </label>
                <input 
                    type="text" 
                    id="location"
                    wire:model="location"
                    placeholder="e.g., Conference Room A, Zoom, etc."
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Color --}}
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Color
                </label>
                <div class="flex items-center gap-3">
                    <input 
                        type="color" 
                        id="color"
                        wire:model="color"
                        class="h-10 w-20 rounded border-gray-300 dark:border-gray-600 shadow-sm"
                    />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $color }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-500">
                        (Default color is set based on type)
                    </span>
                </div>
                @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Attendees Section --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Attendees & Associations</h3>
                
                {{-- Users --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Team Members
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-900">
                        @forelse($availableUsers as $user)
                            <div class="flex items-center mb-2">
                                <input 
                                    type="checkbox" 
                                    id="user_{{ $user->id }}"
                                    wire:model="selectedUsers"
                                    value="{{ $user->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <label for="user_{{ $user->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $user->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No team members assigned to this event.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Speakers --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Speakers
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-900">
                        @forelse($availableSpeakers as $speaker)
                            <div class="flex items-center mb-2">
                                <input 
                                    type="checkbox" 
                                    id="speaker_{{ $speaker->id }}"
                                    wire:model="selectedSpeakers"
                                    value="{{ $speaker->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <label for="speaker_{{ $speaker->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $speaker->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No speakers added to this event.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tags
                    </label>
                    <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-900">
                        @forelse($availableTags as $tag)
                            <div class="flex items-center mb-2">
                                <input 
                                    type="checkbox" 
                                    id="tag_{{ $tag->id }}"
                                    wire:model="selectedTags"
                                    value="{{ $tag->id }}"
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <label for="tag_{{ $tag->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $tag->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No tags available for this event.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('events.calendar.index', $eventId) }}" 
                   class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ $calendarItemId ? 'Update Calendar Item' : 'Create Calendar Item' }}
                </button>
            </div>
        </div>
    </form>
</div>
