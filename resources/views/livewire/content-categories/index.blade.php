<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Content Categories - {{ $event->name }}
            </h2>
            <a href="{{ route('events.content-categories.create', $eventId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Create Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div class="p-6">
                    <!-- Search -->
                    <div class="mb-6">
                        <flux:input 
                            wire:model.live.debounce.300ms="search" 
                            type="text" 
                            placeholder="Search categories..."
                            class="w-full md:w-96"
                        />
                    </div>

                    <!-- Categories List -->
                    @if($categories->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($categories as $category)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-4 h-4 rounded" style="background-color: {{ $category->color }}"></div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $category->name }}
                                            </h3>
                                        </div>
                                        @if($category->is_system)
                                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                                System
                                            </span>
                                        @endif
                                    </div>

                                    @if($category->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            {{ $category->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ $category->contentFiles()->count() }} files
                                        </span>

                                        <div class="flex gap-2">
                                            @if(!$category->is_system)
                                                <flux:button 
                                                    href="{{ route('events.content-categories.edit', ['eventId' => $eventId, 'categoryId' => $category->id]) }}" 
                                                    variant="ghost" 
                                                    size="sm"
                                                >
                                                    Edit
                                                </flux:button>
                                                <flux:button 
                                                    wire:click="confirmDelete({{ $category->id }})" 
                                                    variant="ghost" 
                                                    size="sm"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    Delete
                                                </flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No categories found.</p>
                            <flux:button href="{{ route('events.content-categories.create', $eventId) }}" variant="primary">
                                Create First Category
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $categoryToDelete)
        <flux:modal wire:model.live="showDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Delete Category
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Are you sure you want to delete "{{ $categoryToDelete->name }}"? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <flux:button wire:click="$set('showDeleteModal', false)" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="delete" variant="danger">
                        Delete Category
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
