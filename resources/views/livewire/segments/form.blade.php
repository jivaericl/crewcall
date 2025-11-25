<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $segmentId ? 'Edit Segment' : 'Create Segment' }} - {{ $session->name }}</h2>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <!-- Segment Name -->
                    <div>
                        <flux:label for="name" required>Segment Name</flux:label>
                        <flux:input 
                            wire:model.live="name" 
                            id="name" 
                            type="text" 
                            placeholder="e.g., Walk-in, Speaker 1, Break"
                            class="w-full"
                        />
                        @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Code -->
                    <div>
                        <flux:label for="code">Code</flux:label>
                        <flux:input 
                            wire:model="code" 
                            id="code" 
                            type="text" 
                            placeholder="e.g., SEG-01, BREAK-1"
                            class="w-full"
                        />
                        <flux:description>Optional identifier for this segment</flux:description>
                        @error('code') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Time Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:label required>Start Time</flux:label>
                            <flux:time-picker wire:model.live="start_time" type="input" :dropdown="false" />
                            @error('start_time') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:label required>End Time</flux:label>
                            <flux:time-picker wire:model.live="end_time" type="input" :dropdown="false" />
                            @error('end_time') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Duration Display -->
                    @if($duration)
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                    Duration: {{ $duration }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Client -->
                    <div>
                        <flux:label for="client_id">Client</flux:label>
                        <flux:select wire:model="client_id" id="client_id" class="w-full">
                            <option value="">Select a client...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>User with Client role assigned to this event</flux:description>
                        @error('client_id') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Producer -->
                    <div>
                        <flux:label for="producer_id">Producer</flux:label>
                        <flux:select wire:model="producer_id" id="producer_id" class="w-full">
                            <option value="">Select a producer...</option>
                            @foreach($producers as $producer)
                                <option value="{{ $producer->id }}">{{ $producer->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>Team member assigned to produce this segment</flux:description>
                        @error('producer_id') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Custom Fields -->
                    @if($customFieldsList->count() > 0)
                        <x-custom-fields-form :fields="$customFieldsList" wire:model="customFields" />
                    @endif

                    <!-- Tags -->
                    <div>
                        <flux:label>Tags</flux:label>
                        <flux:description class="mb-3">Select tags to categorize this segment (max 10)</flux:description>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($allTags as $tag)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ in_array($tag->id, $selectedTags) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedTags" 
                                        value="{{ $tag->id }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        {{ count($selectedTags) >= 10 && !in_array($tag->id, $selectedTags) ? 'disabled' : '' }}
                                    >
                                    <span class="ml-2 text-sm font-medium" style="color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($selectedTags) >= 10)
                            <p class="mt-2 text-sm text-amber-600 dark:text-amber-400">Maximum of 10 tags reached</p>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button href="{{ route('sessions.segments.index', $sessionId) }}" variant="ghost">
                            Cancel
                        </flux:button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ $segmentId ? 'Update Segment' : 'Create Segment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
