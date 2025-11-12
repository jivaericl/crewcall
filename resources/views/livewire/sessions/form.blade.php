<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sessionId ? 'Edit Session' : 'Create Session' }}</h2>
            </div>
            <flux:button href="{{ route('events.sessions.index', $eventId) }}" variant="ghost">
                Back to Sessions
            </flux:button>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Session Name *</flux:label>
                                    <flux:input wire:model.blur="name" type="text" placeholder="e.g., Opening Keynote" required />
                                    @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <!-- Code -->
                            <div>
                                <flux:field>
                                    <flux:label>Code</flux:label>
                                    <flux:input wire:model.blur="code" type="text" placeholder="e.g., S01" />
                                    @error('code') <flux:error>{{ $message }}</flux:error> @enderror
                                    <flux:description>Optional session code or identifier</flux:description>
                                </flux:field>
                            </div>

                            <!-- Location -->
                            <div>
                                <flux:field>
                                    <flux:label>Location</flux:label>
                                    <flux:input wire:model.blur="location" type="text" placeholder="e.g., Main Hall" />
                                    @error('location') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <flux:field>
                                    <flux:label>Start Date & Time *</flux:label>
                                    <flux:input wire:model.live="start_date" type="datetime-local" required />
                                    @error('start_date') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <!-- End Date -->
                            <div>
                                <flux:field>
                                    <flux:label>End Date & Time *</flux:label>
                                    <flux:input wire:model.live="end_date" type="datetime-local" required />
                                    @error('end_date') <flux:error>{{ $message }}</flux:error> @enderror
                                    @if($duration)
                                        <flux:description>Duration: {{ $duration }}</flux:description>
                                    @endif
                                </flux:field>
                            </div>

                            <!-- Client -->
                            <div>
                                <flux:field>
                                    <flux:label>Client</flux:label>
                                    <flux:select wire:model="client_id">
                                        <option value="">-- Select Client --</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->full_name }}@if($client->company) - {{ $client->company }}@endif</option>
                                        @endforeach
                                    </flux:select>
                                    @error('client_id') <flux:error>{{ $message }}</flux:error> @enderror
                                    <flux:description>Assign a client contact to this session</flux:description>
                                </flux:field>
                            </div>

                            <!-- Producer -->
                            <div>
                                <flux:field>
                                    <flux:label>Producer</flux:label>
                                    <flux:select wire:model="producer_id">
                                        <option value="">-- Select Producer --</option>
                                        @foreach($producers as $producer)
                                            <option value="{{ $producer->id }}">{{ $producer->full_name }}@if($producer->company) - {{ $producer->company }}@endif</option>
                                        @endforeach
                                    </flux:select>
                                    @error('producer_id') <flux:error>{{ $message }}</flux:error> @enderror
                                    <flux:description>Assign a producer contact to this session</flux:description>
                                </flux:field>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Description</flux:label>
                                    <flux:textarea wire:model.blur="description" rows="4" placeholder="Session description..."></flux:textarea>
                                    @error('description') <flux:error>{{ $message }}</flux:error> @enderror
                                </flux:field>
                            </div>

                            <!-- Tags -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Tags</flux:label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($allTags as $tag)
                                            <label class="inline-flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="selectedTags"
                                                    value="{{ $tag->id }}"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                >
                                                <span class="ml-2 px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                    {{ $tag->name }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <flux:description>Select tags to categorize this session (max 10)</flux:description>
                                </flux:field>
                            </div>

                            <!-- Speakers -->
                            <div class="md:col-span-2">
                                <flux:field>
                                    <flux:label>Speakers</flux:label>
                                    @if($allSpeakers->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($allSpeakers as $speaker)
                                                <label class="inline-flex items-center">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="selectedSpeakers"
                                                        value="{{ $speaker->id }}"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    >
                                                    <span class="ml-2 px-3 py-1 rounded-lg text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ $speaker->name }}
                                                        @if($speaker->full_title)
                                                            <span class="text-xs text-gray-500">- {{ $speaker->full_title }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <flux:description>Select speakers assigned to this session</flux:description>
                                    @else
                                        <p class="text-sm text-gray-500">No speakers available. <a href="{{ route('events.speakers.create', $eventId) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Create a speaker</a></p>
                                    @endif
                                </flux:field>
                            </div>

                            <!-- Custom Fields -->
                            @if($customFields->count() > 0)
                                <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-6 mt-2">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Custom Fields</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($customFields as $field)
                                            <div class="{{ $field->field_type === 'text' && strlen($field->name) > 30 ? 'md:col-span-2' : '' }}">
                                                <flux:field>
                                                    <flux:label>
                                                        {{ $field->name }}
                                                        @if($field->is_required) * @endif
                                                    </flux:label>

                                                    @if($field->field_type === 'text')
                                                        <flux:input 
                                                            wire:model.blur="customFieldValues.{{ $field->id }}" 
                                                            type="text"
                                                        />
                                                    @elseif($field->field_type === 'number')
                                                        <flux:input 
                                                            wire:model.blur="customFieldValues.{{ $field->id }}" 
                                                            type="number"
                                                            step="any"
                                                        />
                                                    @elseif($field->field_type === 'date')
                                                        <flux:input 
                                                            wire:model.blur="customFieldValues.{{ $field->id }}" 
                                                            type="date"
                                                        />
                                                    @elseif($field->field_type === 'select')
                                                        <flux:select 
                                                            wire:model="customFieldValues.{{ $field->id }}"
                                                        >
                                                            <option value="">-- Select --</option>
                                                            @foreach($field->options as $option)
                                                                <option value="{{ $option }}">{{ $option }}</option>
                                                            @endforeach
                                                        </flux:select>
                                                    @elseif($field->field_type === 'checkbox')
                                                        <label class="flex items-center">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model="customFieldValues.{{ $field->id }}"
                                                                value="1"
                                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                            >
                                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                                                        </label>
                                                    @endif

                                                    @error('customFieldValues.' . $field->id) 
                                                        <flux:error>{{ $message }}</flux:error>
                                                    @enderror
                                                </flux:field>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button 
                                href="{{ route('events.sessions.index', $eventId) }}" 
                                variant="ghost"
                                type="button"
                            >
                                Cancel
                            </flux:button>
                            <flux:button type="submit" variant="primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $sessionId ? 'Update Session' : 'Create Session' }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comment Section -->
            @if($sessionId)
                @php
                    $session = \App\Models\Session::find($sessionId);
                @endphp
                @if($session)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-8 p-8">
                        @livewire('comments.comment-section', [
                            'commentable' => $session,
                            'eventId' => $session->event_id
                        ])
                    </div>
                @endif
            @endif
        </div>
        </div>
    </div>
</div>
