<div>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $eventId ? __('Edit Event') : __('Create New Event') }}</h2>
            </div>
            <flux:button href="{{ route('events.index') }}" variant="ghost">
                Back to Events
            </flux:button>
        </div>

        <div>
            @if (session()->has('tag-message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                    {{ session('tag-message') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form wire:submit.prevent="save">
                        <!-- Event Name -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Event Name *</flux:label>
                                <flux:input 
                                    wire:model.blur="name" 
                                    type="text" 
                                    placeholder="e.g., Annual Conference 2025"
                                    required
                                    class="text-lg"
                                />
                                @error('name') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                                <flux:description>A clear, descriptive name for your event</flux:description>
                            </flux:field>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Description</flux:label>
                                <flux:textarea 
                                    wire:model.blur="description" 
                                    rows="5"
                                    placeholder="Provide details about the event, agenda, location, or any other relevant information..."
                                />
                                @error('description') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                                @if($description)
                                    <flux:description>{{ strlen($description) }}/5000 characters</flux:description>
                                @endif
                            </flux:field>
                        </div>

                        <!-- Date and Time Section -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Date & Time</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <!-- Start Date -->
                                <div>
                                    <flux:field>
                                        <flux:label>Start Date & Time *</flux:label>
                                        <flux:input 
                                            wire:model.blur="start_date" 
                                            type="datetime-local"
                                            required
                                        />
                                        @error('start_date') 
                                            <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                    </flux:field>
                                </div>

                                <!-- End Date -->
                                <div>
                                    <flux:field>
                                        <flux:label>End Date & Time *</flux:label>
                                        <flux:input 
                                            wire:model.blur="end_date" 
                                            type="datetime-local"
                                            required
                                        />
                                        @error('end_date') 
                                            <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                    </flux:field>
                                </div>
                            </div>

                            <!-- Duration Display -->
                            @if($duration)
                                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Event Duration: {{ $duration }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Timezone -->
                            <div>
                                <flux:field>
                                    <flux:label>Timezone *</flux:label>
                                    <flux:select wire:model="timezone" required>
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz }}">{{ $tz }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('timezone') 
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                    <flux:description>All times will be displayed in this timezone</flux:description>
                                </flux:field>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        @if($eventId && $customFieldsList->count() > 0)
                            <div class="mb-8">
                                <x-custom-fields-form :fields="$customFieldsList" wire:model="customFields" />
                            </div>
                        @endif

                        <!-- Tags Section -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tags
                                </label>
                                <flux:button 
                                    wire:click="openTagModal" 
                                    variant="ghost" 
                                    size="sm"
                                    type="button"
                                >
                                    + Create New Tag
                                </flux:button>
                            </div>

                            @if($tags->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @foreach($tags as $tag)
                                        <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition
                                            {{ in_array($tag->id, $selectedTags) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                                            <input 
                                                type="checkbox" 
                                                wire:model="selectedTags"
                                                value="{{ $tag->id }}"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                            <span class="ml-2 flex items-center">
                                                <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $tag->color }}"></span>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tag->name }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('selectedTags') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            @else
                                <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400 mb-3">No tags available yet.</p>
                                    <flux:button 
                                        wire:click="openTagModal" 
                                        variant="primary" 
                                        size="sm"
                                        type="button"
                                    >
                                        Create Your First Tag
                                    </flux:button>
                                </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button 
                                href="{{ route('events.index') }}" 
                                variant="ghost"
                                type="button"
                            >
                                Cancel
                            </flux:button>
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $eventId ? 'Update Event' : 'Create Event' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Tag Modal -->
<!-- Create Tag Modal -->
<div x-data="{ show: @entangle('showTagModal') }" 
     x-show="show" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75" 
             @click="$wire.closeTagModal()"
        ></div>

        <!-- Modal panel -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Create New Tag</h3>
            <form wire:submit.prevent="createTag">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tag Name *</label>
                    <input 
                        wire:model="newTagName" 
                        type="text" 
                        placeholder="e.g., Conference, Workshop, Meeting"
                        required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    @error('newTagName') 
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tag Color *</label>
                    <div class="flex items-center gap-3">
                        <input 
                            type="color" 
                            wire:model="newTagColor"
                            class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer"
                        >
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $newTagColor }}</span>
                    </div>
                    @error('newTagColor') 
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button 
                        type="button"
                        wire:click="closeTagModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Create Tag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Comment Section -->
    @if($eventId)
        @php
            $event = \App\Models\Event::find($eventId);
        @endphp
        @if($event)
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 mt-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
                    @livewire('comments.comment-section', [
                        'commentable' => $event,
                        'eventId' => $event->id
                    ])
                </div>
            </div>
        @endif
    @endif

    <!-- Auto-detect timezone script -->
    <script>
        document.addEventListener('livewire:init', () => {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            @this.call('setTimezone', timezone);
        });
    </script>
        </div>
    </div>
</div>
