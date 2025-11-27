<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">     <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tags</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage tags for {{ $event->name }}
                </p>
            </div>
            <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Tag
            </button>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Search -->
                <div>
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search tags..." />
                </div>
                
                <!-- Filter by Type -->
                <div>
                    <select wire:model.live="filterType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="event">Event</option>
                        <option value="session">Session</option>
                        <option value="segment">Segment</option>
                        <option value="cue">Cue</option>
                        <option value="content">Content</option>
                        <option value="contact">Contact</option>
                        <option value="speaker">Speaker</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tags Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            @if($tags->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                        <th wire:click="changeSortField('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-1">
                                Tag
                                @if($sortBy === 'name')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="changeSortField('model_type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                            <div class="flex items-center gap-1">
                                Type
                                @if($sortBy === 'model_type')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                        </thead>

                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($tags as $tag)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <!-- Tag -->
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                        style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 dark:text-white capitalize">{{ $tag->model_type ?? 'Event' }}</span>
                                </td>

                                <!-- Description -->
                                <td class="px-6 py-4">
                                    @if($tag->description)
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $tag->description }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <!-- Usage -->
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $tag->sessions_count + $tag->segments_count + $tag->cues_count + $tag->speakers_count + $tag->contacts_count }} items
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($tag->sessions_count > 0) {{ $tag->sessions_count }} sessions @endif
                                        @if($tag->segments_count > 0) {{ $tag->segments_count }} segments @endif
                                        @if($tag->cues_count > 0) {{ $tag->cues_count }} cues @endif
                                        @if($tag->speakers_count > 0) {{ $tag->speakers_count }} speakers @endif
                                        @if($tag->contacts_count > 0) {{ $tag->contacts_count }} contacts @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $tag->id }})" square>
                                            <x-action-icon action="edit" />
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $tag->id }})" square>
                                            <x-action-icon action="delete" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tags->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No tags</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new tag.</p>
                    <div class="mt-6">
                        <flux:button wire:click="openCreateModal" variant="primary">
                            <flux:icon.plus class="w-5 h-5" />
                            Create Tag
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $tagId ? 'Edit Tag' : 'Create Tag' }}
                </h3>
                
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tag Name *</label>
                            <input type="text" wire:model="name" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model Type *</label>
                            <select wire:model="model_type" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="event">Event</option>
                                <option value="session">Session</option>
                                <option value="segment">Segment</option>
                                <option value="cue">Cue</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tags can only be assigned to the selected model type</p>
                            @error('model_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color *</label>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model.live="color" class="h-10 w-20 rounded border-gray-300 dark:border-gray-600" />
                                <span class="px-3 py-1 rounded-full text-sm font-medium"
                                    style="background-color: {{ $color }}20; color: {{ $color }}">
                                    {{ $name ?: 'Preview' }}
                                </span>
                            </div>
                            @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>


                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                            {{ $tagId ? 'Update Tag' : 'Create Tag' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Tag</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete this tag? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="delete" class="px-4 py-2 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white font-medium rounded-md transition">
                        Delete Tag
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
