<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Content Library - {{ $event->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Manage media files, presentations, and documents
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Header Actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-2">
                            <flux:button href="{{ route('events.index') }}" variant="ghost">
                                ← Back to Events
                            </flux:button>
                        </div>
                        <flux:button wire:click="openUploadModal" variant="primary">
                            Upload File
                        </flux:button>
                    </div>

                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <flux:input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                placeholder="Search files..." 
                                class="w-full"
                            />
                        </div>
                        <div>
                            <flux:select wire:model.live="typeFilter" class="w-full">
                                <option value="">All Types</option>
                                <option value="audio">Audio</option>
                                <option value="video">Video</option>
                                <option value="presentation">Presentation</option>
                                <option value="document">Document</option>
                                <option value="image">Image</option>
                                <option value="other">Other</option>
                            </flux:select>
                        </div>
                        <div>
                            <flux:select wire:model.live="categoryFilter" class="w-full">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Files Grid -->
                    @if($files->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach($files as $file)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-3xl">{{ $file->file_type_icon }}</span>
                                            <div>
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $file->name }}</h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $file->formatted_file_size }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($file->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $file->description }}</p>
                                    @endif

                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst($file->file_type) }}
                                        </span>
                                        @if($file->category)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $file->category->color }}20; color: {{ $file->category->color }}">
                                                {{ $file->category->name }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                v{{ $file->current_version }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                        Uploaded by {{ $file->creator?->name ?? 'Unknown' }} on {{ $file->created_at->format('M d, Y') }}
                                    </div>

                                    <div class="flex gap-1">
                                        <flux:button href="{{ $file->download_url }}" target="_blank" variant="ghost" size="sm" title="Download">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </flux:button>
                                        <flux:button href="{{ route('events.content.edit', ['eventId' => $eventId, 'contentId' => $file->id]) }}" variant="ghost" size="sm" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="viewVersions({{ $file->id }})" variant="ghost" size="sm" title="Versions">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </flux:button>
                                        <flux:button wire:click="confirmDelete({{ $file->id }})" variant="danger" size="sm" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $files->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No files found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading a file.</p>
                            <div class="mt-6">
                                <flux:button wire:click="openUploadModal" variant="primary">
                                    Upload File
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="uploadFileSubmit">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Upload File</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <flux:label for="uploadFile" required>File</flux:label>
                                    <input type="file" wire:model="uploadFile" id="uploadFile" class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700">
                                    @error('uploadFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max size: 500MB</p>
                                </div>

                                <div>
                                    <flux:label for="uploadName" required>Name</flux:label>
                                    <flux:input wire:model="uploadName" id="uploadName" type="text" class="w-full" />
                                    @error('uploadName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <flux:label for="uploadType" required>File Type</flux:label>
                                    <flux:select wire:model="uploadType" id="uploadType" class="w-full">
                                        <option value="">Select type...</option>
                                        <option value="audio">Audio</option>
                                        <option value="video">Video</option>
                                        <option value="presentation">Presentation</option>
                                        <option value="document">Document</option>
                                        <option value="image">Image</option>
                                        <option value="other">Other</option>
                                    </flux:select>
                                    @error('uploadType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <flux:label for="uploadCategory">Category</flux:label>
                                    <flux:select wire:model="uploadCategory" id="uploadCategory" class="w-full">
                                        <option value="">No category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <div>
                                    <flux:label for="uploadDescription">Description</flux:label>
                                    <flux:textarea wire:model="uploadDescription" id="uploadDescription" rows="3" class="w-full" />
                                </div>

                                <div>
                                    <flux:label>Assign to Speakers</flux:label>
                                    @if($allSpeakers->count() > 0)
                                        <div class="mt-2 space-y-1 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-2">
                                            @foreach($allSpeakers as $speaker)
                                                <label class="flex items-center gap-2">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="uploadSpeakers"
                                                        value="{{ $speaker->id }}"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    >
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $speaker->full_name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 mt-2">No speakers available.</p>
                                    @endif
                                </div>

                                <div>
                                    <flux:label>Assign to Segments</flux:label>
                                    @if($allSegments->count() > 0)
                                        <div class="mt-2 space-y-1 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-2">
                                            @foreach($allSegments as $segment)
                                                <label class="flex items-center gap-2">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="uploadSegments"
                                                        value="{{ $segment->id }}"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm"
                                                    >
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $segment->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 mt-2">No segments available.</p>
                                    @endif
                                </div>

                                <div>
                                    <flux:label>Assign to Cues</flux:label>
                                    @if($allCues->count() > 0)
                                        <div class="mt-2 space-y-1 max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-2">
                                            @foreach($allCues as $cue)
                                                <label class="flex items-center gap-2">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="uploadCues"
                                                        value="{{ $cue->id }}"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm"
                                                    >
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $cue->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 mt-2">No cues available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <flux:button type="submit" variant="primary" class="ml-3">Upload</flux:button>
                            <flux:button wire:click="closeUploadModal" type="button" variant="ghost">Cancel</flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Version History Modal -->
    @if($viewingVersions && $versionedFile)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Version History - {{ $versionedFile->name }}</h3>
                        <div class="space-y-3">
                            @foreach($versionedFile->versions as $version)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                Version {{ $version->version_number }}
                                                @if($version->version_number == $versionedFile->current_version)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Current
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $version->formatted_file_size }} • Uploaded by {{ $version->uploader?->name ?? 'Unknown' }} on {{ $version->created_at->format('M d, Y g:i A') }}
                                            </div>
                                            @if($version->change_notes)
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $version->change_notes }}</div>
                                            @endif
                                        </div>
                                        <flux:button href="{{ $version->download_url }}" target="_blank" variant="ghost" size="sm">
                                            Download
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6">
                        <flux:button wire:click="closeVersions" variant="ghost">Close</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($deleteId)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Delete File</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this file? This will delete all versions. This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <flux:button wire:click="deleteFile" variant="danger" class="ml-3">Delete</flux:button>
                        <flux:button wire:click="$set('deleteId', null)" variant="ghost">Cancel</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
