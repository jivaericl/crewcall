<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tags</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage tags for {{ $event->name }}
                </p>
            </div>
            <flux:button wire:click="openCreateModal" variant="primary">
                <flux:icon.plus class="w-5 h-5" />
                Create Tag
            </flux:button>
        </div>

        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search tags..." />
        </div>

        <!-- Tags Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            @if($tags->count() > 0)
                <flux:table>
                    <flux:columns>
                        <flux:column>Tag</flux:column>
                        <flux:column>Description</flux:column>
                        <flux:column align="center">Usage</flux:column>
                        <flux:column align="end">Actions</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($tags as $tag)
                            <flux:row wire:key="tag-{{ $tag->id }}">
                                <!-- Tag -->
                                <flux:cell>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                        style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </flux:cell>

                                <!-- Description -->
                                <flux:cell>
                                    @if($tag->description)
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $tag->description }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </flux:cell>

                                <!-- Usage -->
                                <flux:cell align="center">
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
                                </flux:cell>

                                <!-- Actions -->
                                <flux:cell align="end">
                                    <div class="flex items-center gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $tag->id }})">
                                            Edit
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $tag->id }})">
                                            Delete
                                        </flux:button>
                                    </div>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>

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
        <flux:modal wire:model.live="showModal">
            <flux:modal.content>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $tagId ? 'Edit Tag' : 'Create Tag' }}
                </h3>
                
                <form wire:submit="save">
                    <div class="space-y-4">
                        <div>
                            <flux:label>Tag Name *</flux:label>
                            <flux:input wire:model="name" required />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Color *</flux:label>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model.live="color" class="h-10 w-20 rounded border-gray-300 dark:border-gray-600" />
                                <span class="px-3 py-1 rounded-full text-sm font-medium"
                                    style="background-color: {{ $color }}20; color: {{ $color }}">
                                    {{ $name ?: 'Preview' }}
                                </span>
                            </div>
                            @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Description</flux:label>
                            <flux:textarea wire:model="description" rows="3" />
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <flux:button type="button" variant="ghost" wire:click="closeModal">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $tagId ? 'Update Tag' : 'Create Tag' }}
                        </flux:button>
                    </div>
                </form>
            </flux:modal.content>
        </flux:modal>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <flux:modal wire:model.live="showDeleteModal">
            <flux:modal.content>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Tag</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete this tag? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </flux:button>
                    <flux:button type="button" variant="danger" wire:click="delete">
                        Delete Tag
                    </flux:button>
                </div>
            </flux:modal.content>
        </flux:modal>
    @endif
</div>
