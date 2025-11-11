<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cueTypeId ? 'Edit' : 'Create' }} Cue Type</h2>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <flux:input 
                            wire:model="name" 
                            label="Name"
                            placeholder="e.g., Audio, Lights, Video"
                            required
                        />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <flux:textarea 
                            wire:model="description" 
                            label="Description"
                            placeholder="Brief description of this cue type..."
                            rows="3"
                        />
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color
                        </label>
                        <div class="flex items-center gap-4">
                            <input 
                                type="color" 
                                wire:model="color" 
                                class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600"
                            />
                            <flux:input 
                                wire:model="color" 
                                type="text"
                                placeholder="#3B82F6"
                                class="flex-1"
                            />
                        </div>
                        @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Icon -->
                    <div>
                        <flux:input 
                            wire:model="icon" 
                            label="Icon (optional)"
                            placeholder="e.g., volume-up, lightbulb, play-circle"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Icon name from your icon library
                        </p>
                        @error('icon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <flux:input 
                            wire:model="sort_order" 
                            type="number"
                            label="Sort Order"
                            min="0"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Lower numbers appear first
                        </p>
                        @error('sort_order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Is Active -->
                    <div>
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="is_active" 
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Inactive cue types won't appear in dropdowns
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-2">
                        <flux:button href="{{ route('events.cue-types.index', $eventId) }}" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $cueTypeId ? 'Update' : 'Create' }} Cue Type
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
