<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Event Resources - {{ $event->name }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage resources for this event
                </p>
            </div>
        </div>

        <div>
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded">
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
                        <button wire:click="openUploadModal" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Add Resource
                        </button>
                    </div>

                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <flux:input
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                placeholder="Search resources..."
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

                    <!-- Files Table -->
                    @if($files->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resource</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Version</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Uploaded</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($files as $file)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                                                        @switch($file->file_type)
                                                            @case('audio')
                                                                <x-lineicon alias="content.audio" size="w-5 h-5" />
                                                                @break
                                                            @case('video')
                                                                <x-lineicon alias="content.video" size="w-5 h-5" />
                                                                @break
                                                            @case('presentation')
                                                                <x-lineicon alias="content.presentation" size="w-5 h-5" />
                                                                @break
                                                            @case('document')
                                                                <x-lineicon alias="content.document" size="w-5 h-5" />
                                                                @break
                                                            @case('image')
                                                                <x-lineicon alias="content.image" size="w-5 h-5" />
                                                                @break
                                                            @case('rich_text')
                                                                <x-lineicon alias="content.rich_text" size="w-5 h-5" />
                                                                @break
                                                            @case('plain_text')
                                                                <x-lineicon alias="content.text" size="w-5 h-5" />
                                                                @break
                                                            @case('url')
                                                                <x-lineicon alias="content.url" size="w-5 h-5" />
                                                                @break
                                                            @default
                                                                <x-lineicon alias="content.file" size="w-5 h-5" />
                                                        @endswitch
                                                    </span>
                                                    <div>
                                                        <div class="font-medium text-gray-900 dark:text-white">{{ $file->name }}</div>
                                                        @if($file->description)
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($file->description, 50) }}</div>
                                                        @endif
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $file->formatted_file_size }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                    {{ ucfirst(str_replace('_', ' ', $file->file_type)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($file->category)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $file->category->color }}25; color: {{ $file->category->color }};">
                                                        {{ $file->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">None</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    v{{ $file->current_version }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-200">
                                                    {{ $file->creator ? $file->creator->name : 'Unknown' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $file->created_at->format('M j, Y g:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                                <a href="{{ $file->download_url }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Download">
                                                    <x-lineicon alias="actions.download" size="w-5 h-5" />
                                                </a>
                                                <a href="{{ route('events.resources.edit', ['eventId' => $eventId, 'contentId' => $file->id]) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="Edit">
                                                    <x-lineicon alias="actions.edit" size="w-5 h-5" />
                                                </a>
                                                <button wire:click="viewVersions({{ $file->id }})" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300" title="Version History">
                                                    <x-lineicon alias="actions.history" size="w-5 h-5" />
                                                </button>
                                                <button wire:click="confirmDelete({{ $file->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Delete">
                                                    <x-lineicon alias="actions.delete" size="w-5 h-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $files->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-lineicon alias="content.file" size="w-16 h-16" class="mx-auto text-gray-400 dark:text-gray-600" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">No resources found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if($search || $typeFilter || $categoryFilter)
                                    No resources match your current filters. Try adjusting your search or filters.
                                @else
                                    Get started by adding your first resource.
                                @endif
                            </p>
                            <div class="mt-6">
                                <button wire:click="openUploadModal" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-lineicon alias="actions.add" size="w-5 h-5" class="-ml-1 mr-2" />
                                    Add Resource
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-data="{ show: @entangle('showUploadModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Add Resource
                </h3>

                <form wire:submit.prevent="uploadFileSubmit" class="space-y-4">
                    <!-- Content Type - First Question -->
                    <div>
                        <flux:label for="uploadType" required>Resource Type</flux:label>
                        <flux:select wire:model.live="uploadType" id="uploadType" class="w-full">
                            <option value="">Select resource type</option>
                            <optgroup label="Files">
                                <option value="audio">Audio File</option>
                                <option value="video">Video File</option>
                                <option value="presentation">Presentation File</option>
                                <option value="document">Document File</option>
                                <option value="image">Image File</option>
                                <option value="other">Other File</option>
                            </optgroup>
                            <optgroup label="Text & Links">
                                <option value="url">URL/Link</option>
                                <option value="rich_text">Rich Text (HTML)</option>
                                <option value="plain_text">Plain Text</option>
                            </optgroup>
                        </flux:select>
                        @error('uploadType') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    @if($uploadType)
                        <!-- File Upload - Only for file types -->
                        @if(in_array($uploadType, ['audio', 'video', 'presentation', 'document', 'image', 'other']))
                            <div>
                                <flux:label required>Select File</flux:label>
                                <flux:file-upload.dropzone wire:model="uploadFile" />
                                @error('uploadFile') <flux:error>{{ $message }}</flux:error> @enderror
                            </div>
                        @endif

                        <!-- URL Input - Only for URL type -->
                        @if($uploadType === 'url')
                            <div>
                                <flux:label for="uploadContent" required>URL/Link</flux:label>
                                <flux:input
                                    wire:model.blur="uploadContent"
                                    id="uploadContent"
                                    type="url"
                                    placeholder="https://example.com"
                                    class="w-full"
                                />
                                @error('uploadContent') <flux:error>{{ $message }}</flux:error> @enderror
                            </div>
                        @endif

                        <!-- Rich Text Editor - Only for rich_text type -->
                        @if($uploadType === 'rich_text')
                            <div>
                                <flux:label required>Content (HTML)</flux:label>
                                <div wire:ignore class="bg-white rounded-md border border-gray-300 dark:border-gray-600" style="min-height: 350px;">
                                    <div id="quill-editor-container" style="min-height: 350px;"></div>
                                </div>
                                <input type="hidden" wire:model="uploadContent" id="quill-hidden-input">
                                @error('uploadContent') <flux:error>{{ $message }}</flux:error> @enderror
                            </div>
                        @endif

                        <!-- Plain Text - Only for plain_text type -->
                        @if($uploadType === 'plain_text')
                            <div>
                                <flux:label for="uploadContent" required>Content</flux:label>
                                <flux:textarea
                                    wire:model.blur="uploadContent"
                                    id="uploadContent"
                                    rows="10"
                                    class="w-full"
                                />
                                @error('uploadContent') <flux:error>{{ $message }}</flux:error> @enderror
                            </div>
                        @endif

                        <!-- Name -->
                        <div>
                            <flux:label for="uploadName" required>Name</flux:label>
                            <flux:input
                                wire:model.blur="uploadName"
                                id="uploadName"
                                type="text"
                                class="w-full"
                            />
                            @error('uploadName') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <flux:label for="uploadDescription">Description</flux:label>
                            <flux:textarea
                                wire:model.blur="uploadDescription"
                                id="uploadDescription"
                                rows="3"
                                class="w-full"
                            />
                            @error('uploadDescription') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <flux:label for="uploadCategory">Category</flux:label>
                            <flux:select wire:model.blur="uploadCategory" id="uploadCategory" class="w-full">
                                <option value="">No category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('uploadCategory') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeUploadModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        @if($uploadType)
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                {{ in_array($uploadType, ['audio', 'video', 'presentation', 'document', 'image', 'other']) ? 'Upload File' : 'Add Resource' }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($deleteId)
        <div class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <x-lineicon alias="actions.warning" size="w-6 h-6" class="text-red-600 dark:text-red-400" />
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Delete Resource
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete this resource? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="deleteFile" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button wire:click="confirmDelete(null)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Version History Modal -->
    @if($viewingVersions)
        <div class="fixed inset-0 overflow-y-auto z-50">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div>
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Version History
                                </h3>
                                <button wire:click="closeVersions" class="text-gray-400 hover:text-gray-500">
                                    <x-lineicon alias="actions.close" size="w-5 h-5" />
                                </button>
                            </div>
                            <div class="mt-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $versionedFile->name }}
                                </div>
                                <div class="space-y-4">
                                    @foreach($versionedFile->versions->sortByDesc('version_number') as $version)
                                        <div class="flex items-start p-3 border border-gray-200 dark:border-gray-700 rounded-lg {{ $version->version_number == $versionedFile->current_version ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $version->version_number == $versionedFile->current_version ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                        v{{ $version->version_number }}
                                                        @if($version->version_number == $versionedFile->current_version)
                                                            <span class="ml-1 text-xs">(Current)</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                                    {{ $version->change_notes }}
                                                </div>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $version->formatted_file_size }} •
                                                    Uploaded by {{ $version->uploader ? $version->uploader->name : 'Unknown' }} •
                                                    {{ $version->created_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                            <div>
                                                <a href="{{ $version->download_url }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Download">
                                                    <x-lineicon alias="actions.download" size="w-5 h-5" />
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6">
                        <button wire:click="closeVersions" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quill.js for Rich Text Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
    #quill-editor-container .ql-toolbar {
        background: #f3f4f6;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        border: 1px solid #d1d5db;
        border-bottom: none;
    }
    #quill-editor-container .ql-container {
        background: white;
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        font-size: 14px;
        border: 1px solid #d1d5db;
    }
    #quill-editor-container .ql-editor {
        min-height: 250px;
        max-height: 500px;
        overflow-y: auto;
    }
    #quill-editor-container .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: normal;
    }
</style>
<script>
    (function() {
        let quillInstance = null;
        let checkCount = 0;
        const maxChecks = 50; // Check for 5 seconds (50 * 100ms)

        function tryInitQuill() {
            // Check if Quill library is loaded
            if (typeof Quill === 'undefined') {
                console.log('Quill library not loaded yet...');
                if (checkCount < maxChecks) {
                    checkCount++;
                    setTimeout(tryInitQuill, 100);
                }
                return;
            }

            const container = document.getElementById('quill-editor-container');

            // Check if container exists
            if (!container) {
                if (checkCount < maxChecks) {
                    checkCount++;
                    setTimeout(tryInitQuill, 100);
                }
                return;
            }

            // Check if already initialized
            if (container.classList.contains('ql-container')) {
                console.log('Quill already initialized');
                return;
            }

            // Initialize Quill
            try {
                console.log('Initializing Quill editor...');
                quillInstance = new Quill('#quill-editor-container', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'color': [] }, { 'background': [] }],
                            ['link'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Enter your content here...'
                });

                console.log('Quill editor initialized successfully!');

                // Update hidden input on text change
                quillInstance.on('text-change', function() {
                    const html = quillInstance.root.innerHTML;
                    const hiddenInput = document.getElementById('quill-hidden-input');
                    if (hiddenInput) {
                        hiddenInput.value = html;
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            } catch (error) {
                console.error('Error initializing Quill:', error);
            }
        }

        // Start checking immediately and repeatedly
        setInterval(tryInitQuill, 100);
    })();
    </script>
</div>
