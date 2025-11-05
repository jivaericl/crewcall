<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Content - {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <!-- Current File Info -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Current File</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <p><strong>Version:</strong> {{ $content->current_version }}</p>
                            <p><strong>Size:</strong> {{ $content->formatted_file_size }}</p>
                            <p><strong>Type:</strong> {{ $content->mime_type }}</p>
                            <p><strong>Uploaded:</strong> {{ $content->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div class="mt-3">
                            <flux:button wire:click="openVersionModal" variant="primary" size="sm">
                                Upload New Version
                            </flux:button>
                        </div>
                    </div>

                    <!-- Name -->
                    <div>
                        <flux:label for="name" required>Content Name</flux:label>
                        <flux:input 
                            wire:model.blur="name" 
                            id="name" 
                            type="text" 
                            class="w-full"
                        />
                        @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <flux:label for="description">Description</flux:label>
                        <flux:textarea 
                            wire:model.blur="description" 
                            id="description" 
                            rows="3"
                            class="w-full"
                        />
                        @error('description') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Category and Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:label for="category_id">Category</flux:label>
                            <flux:select wire:model.blur="category_id" id="category_id" class="w-full">
                                <option value="">No category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('category_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:label for="file_type" required>File Type</flux:label>
                            <flux:select wire:model.blur="file_type" id="file_type" class="w-full">
                                <option value="audio">Audio</option>
                                <option value="video">Video</option>
                                <option value="presentation">Presentation</option>
                                <option value="document">Document</option>
                                <option value="image">Image</option>
                                <option value="other">Other</option>
                            </flux:select>
                            @error('file_type') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Speakers -->
                    <div>
                        <flux:label>Associated Speakers</flux:label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-3">
                            @forelse($allSpeakers as $speaker)
                                <label class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="selectedSpeakers" 
                                        value="{{ $speaker->id }}"
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $speaker->full_name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No speakers available</p>
                            @endforelse
                        </div>
                        @error('selectedSpeakers') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Segments -->
                    <div>
                        <flux:label>Associated Segments</flux:label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-3">
                            @forelse($allSegments as $segment)
                                <label class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="selectedSegments" 
                                        value="{{ $segment->id }}"
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $segment->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No segments available</p>
                            @endforelse
                        </div>
                        @error('selectedSegments') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Cues -->
                    <div>
                        <flux:label>Associated Cues</flux:label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-3">
                            @forelse($allCues as $cue)
                                <label class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="selectedCues" 
                                        value="{{ $cue->id }}"
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $cue->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No cues available</p>
                            @endforelse
                        </div>
                        @error('selectedCues') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="flex items-center gap-2">
                            <input 
                                wire:model.blur="is_active" 
                                type="checkbox" 
                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button 
                            href="{{ route('events.content.index', $eventId) }}" 
                            variant="ghost"
                        >
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Update Content
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>    <!-- Upload New Version Modal -->
    <div x-data="{ show: @entangle('showVersionModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Upload New Version
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <flux:label for="newVersionFile" required>Select File</flux:label>
                        <input 
                            wire:model="newVersionFile" 
                            type="file" 
                            id="newVersionFile"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100"
                        />
                        @error('newVersionFile') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <div>
                        <flux:label for="changeNotes">Change Notes</flux:label>
                        <flux:textarea 
                            wire:model.blur="changeNotes" 
                            id="changeNotes" 
                            rows="3"
                            placeholder="Describe what changed in this version..."
                            class="w-full"
                        />
                        @error('changeNotes') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>
                </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showVersionModal', false)" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="button" wire:click="uploadNewVersion" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Upload Version
                        </button>
                    </div>
                </div>
        </div>
    </div>