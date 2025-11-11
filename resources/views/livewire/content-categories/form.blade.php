<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $categoryId ? 'Edit Category' : 'Create Category' }} - {{ $event->name }}</h2>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <!-- Category Name -->
                    <div>
                        <flux:label for="name" required>Category Name</flux:label>
                        <flux:input 
                            wire:model.blur="name" 
                            id="name" 
                            type="text" 
                            placeholder="e.g., Presentations, Videos, Graphics"
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
                            placeholder="Optional description of this category..."
                            class="w-full"
                        />
                        @error('description') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <flux:label for="color" required>Color</flux:label>
                        <div class="flex items-center gap-4">
                            <input 
                                wire:model.blur="color" 
                                id="color" 
                                type="color" 
                                class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600"
                            />
                            <flux:input 
                                wire:model.blur="color" 
                                type="text" 
                                placeholder="#3b82f6"
                                class="flex-1"
                            />
                        </div>
                        <flux:description>Choose a color to identify this category</flux:description>
                        @error('color') <flux:error>{{ $message }}</flux:error> @enderror
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
                        <flux:description>Inactive categories won't be available for new content</flux:description>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <flux:button 
                            href="{{ route('events.content-categories.index', $eventId) }}" 
                            variant="ghost"
                        >
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $categoryId ? 'Update Category' : 'Create Category' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
