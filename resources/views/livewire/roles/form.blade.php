<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $roleId ? __('Edit Role') : __('Create Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit.prevent="save">
                        <!-- Role Name -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Role Name *</flux:label>
                                <flux:input 
                                    wire:model="name" 
                                    type="text" 
                                    placeholder="Enter role name"
                                    required
                                />
                                @error('name') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Description</flux:label>
                                <flux:textarea 
                                    wire:model="description" 
                                    rows="3"
                                    placeholder="Enter role description"
                                />
                                @error('description') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Permissions
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <input 
                                        type="checkbox" 
                                        wire:model="can_view"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">View</span>
                                </label>

                                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <input 
                                        type="checkbox" 
                                        wire:model="can_add"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Add</span>
                                </label>

                                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <input 
                                        type="checkbox" 
                                        wire:model="can_edit"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Edit</span>
                                </label>

                                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <input 
                                        type="checkbox" 
                                        wire:model="can_delete"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Delete</span>
                                </label>
                            </div>
                        </div>

                        <!-- Status and Sort Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Active Status -->
                            <div>
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_active"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Role is Active</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Inactive roles cannot be assigned to events
                                </p>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <flux:field>
                                    <flux:label>Sort Order</flux:label>
                                    <flux:input 
                                        wire:model="sort_order" 
                                        type="number" 
                                        min="0"
                                        placeholder="0"
                                    />
                                    @error('sort_order') 
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                </flux:field>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Lower numbers appear first in lists
                                </p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button 
                                href="{{ route('roles.index') }}" 
                                variant="ghost"
                                type="button"
                            >
                                Cancel
                            </flux:button>
                            <flux:button 
                                type="submit" 
                                variant="primary"
                            >
                                {{ $roleId ? 'Update Role' : 'Create Role' }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
