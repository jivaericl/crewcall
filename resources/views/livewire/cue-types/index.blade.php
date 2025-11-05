<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Cue Types for {{ $event->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Manage event-specific cue types
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('events.dashboard', $eventId) }}" variant="ghost">
                    Back to Event
                </flux:button>
                <flux:button href="{{ route('events.cue-types.create', $eventId) }}" variant="primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Cue Type
                </flux:button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Search -->
                    <div class="mb-6">
                        <flux:input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search cue types..."
                            class="w-full"
                        />
                    </div>

                    @if($cueTypes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($cueTypes as $cueType)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <div class="w-4 h-4 rounded" style="background-color: {{ $cueType->color }}"></div>
                                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $cueType->name }}
                                                </h3>
                                                @if($cueType->is_system)
                                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded">
                                                        System
                                                    </span>
                                                @endif
                                            </div>
                                            @if($cueType->description)
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $cueType->description }}
                                                </p>
                                            @endif
                                            <div class="mt-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                @if($cueType->icon)
                                                    <span>Icon: {{ $cueType->icon }}</span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded {{ $cueType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                    {{ $cueType->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if(!$cueType->is_system)
                                            <div class="flex gap-1 ml-2">
                                                <flux:button href="{{ route('events.cue-types.edit', [$eventId, $cueType->id]) }}" variant="ghost" size="sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </flux:button>
                                                <flux:button wire:click="confirmDelete({{ $cueType->id }})" variant="danger" size="sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $cueTypes->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No cue types</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new cue type.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <flux:modal wire:model="showDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Confirm Delete</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete this cue type? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('showDeleteModal', false)" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="deleteCueType" variant="danger">
                        Delete
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
