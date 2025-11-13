<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Content - {{ $event->name }}</h2>
            </div>
        </div>

        <div>
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
                            <button wire:click="openVersionModal" type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Upload New Version
                            </button>
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
                            <flux:select wire:model.live="file_type" id="file_type" class="w-full">
                                <option value="audio">Audio</option>
                                <option value="video">Video</option>
                                <option value="presentation">Presentation</option>
                                <option value="document">Document</option>
                                <option value="image">Image</option>
                                <option value="rich_text">Rich Text (HTML)</option>
                                <option value="plain_text">Plain Text</option>
                                <option value="url">URL</option>
                                <option value="other">Other</option>
                            </flux:select>
                            @error('file_type') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Rich Text Content -->
                    @if($file_type === 'rich_text')
                        <div>
                            <flux:label for="quill-editor" required>Rich Text Content</flux:label>
                            <div wire:ignore>
                                <div 
                                    id="quill-editor"
                                    style="min-height: 300px; background: white;"
                                    class="rounded-md border border-gray-300 dark:border-gray-600"
                                ></div>
                                <input type="hidden" wire:model="content_text" id="content_text_hidden">
                            </div>
                            @error('content_text') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    @endif

                    <!-- Plain Text Content -->
                    @if($file_type === 'plain_text')
                        <div>
                            <flux:label for="content_text" required>Content</flux:label>
                            <flux:textarea 
                                wire:model.blur="content_text" 
                                id="content_text" 
                                rows="10"
                                class="w-full"
                            />
                            @error('content_text') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    @endif

                    <!-- URL Content -->
                    @if($file_type === 'url')
                        <div>
                            <flux:label for="content_text" required>URL</flux:label>
                            <flux:input 
                                wire:model.blur="content_text" 
                                id="content_text" 
                                type="url"
                                placeholder="https://example.com"
                                class="w-full"
                            />
                            @error('content_text') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    @endif

                    <!-- Tags -->
                    <div>
                        <flux:label>Tags</flux:label>
                        <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded p-3">
                            @forelse($allTags as $tag)
                                <label class="flex items-center gap-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model.blur="selectedTags" 
                                        value="{{ $tag->id }}"
                                        class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No tags available</p>
                            @endforelse
                        </div>
                        @error('selectedTags') <flux:error>{{ $message }}</flux:error> @enderror
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

                    <!-- Custom Fields -->
                    @if($customFieldsList->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Custom Fields</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($customFieldsList as $field)
                                    <div class="{{ $field->field_type === 'textarea' ? 'md:col-span-2' : '' }}">
                                        <flux:field>
                                            <flux:label for="custom_{{ $field->id }}">
                                                {{ $field->name }}
                                                @if($field->is_required) <span class="text-red-500">*</span> @endif
                                            </flux:label>
                                            
                                            @if($field->field_type === 'text')
                                                <flux:input 
                                                    wire:model="customFields.{{ $field->id }}" 
                                                    id="custom_{{ $field->id }}"
                                                    type="text"
                                                />
                                            @elseif($field->field_type === 'number')
                                                <flux:input 
                                                    wire:model="customFields.{{ $field->id }}" 
                                                    id="custom_{{ $field->id }}"
                                                    type="number"
                                                    step="any"
                                                />
                                            @elseif($field->field_type === 'date')
                                                <flux:input 
                                                    wire:model="customFields.{{ $field->id }}" 
                                                    id="custom_{{ $field->id }}"
                                                    type="date"
                                                />
                                            @elseif($field->field_type === 'textarea')
                                                <flux:textarea 
                                                    wire:model="customFields.{{ $field->id }}" 
                                                    id="custom_{{ $field->id }}"
                                                    rows="3"
                                                />
                                            @elseif($field->field_type === 'select')
                                                <flux:select wire:model="customFields.{{ $field->id }}" id="custom_{{ $field->id }}">
                                                    <option value="">-- Select --</option>
                                                    @if($field->options)
                                                        @foreach($field->options as $option)
                                                            <option value="{{ $option }}">{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </flux:select>
                                            @elseif($field->field_type === 'checkbox')
                                                <div class="flex items-center">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:model="customFields.{{ $field->id }}"
                                                        id="custom_{{ $field->id }}"
                                                        value="1"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    >
                                                </div>
                                            @endif
                                            
                                            @error('customFields.' . $field->id) 
                                                <flux:error>{{ $message }}</flux:error>
                                            @enderror
                                        </flux:field>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Version History -->
                    @if($content->versions->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Version History</h3>
                            <div class="space-y-3">
                                @foreach($content->versions as $version)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg {{ $version->version_number == $content->current_version ? 'ring-2 ring-blue-500' : '' }}">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                                    Version {{ $version->version_number }}
                                                </span>
                                                @if($version->version_number == $content->current_version)
                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded">
                                                        Current
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <p><strong>Size:</strong> {{ $version->formatted_file_size }}</p>
                                                <p><strong>Uploaded:</strong> {{ $version->created_at->format('M d, Y g:i A') }} by {{ $version->uploader->name ?? 'Unknown' }}</p>
                                                @if($version->change_notes)
                                                    <p><strong>Notes:</strong> {{ $version->change_notes }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button 
                                                wire:click="downloadVersion({{ $version->id }})"
                                                type="button"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 px-3 py-1.5 text-sm"
                                            >
                                                Download
                                            </button>
                                            @if($version->version_number != $content->current_version)
                                                <button 
                                                    wire:click="restoreVersion({{ $version->version_number }})" 
                                                    type="button"
                                                    wire:confirm="Are you sure you want to restore version {{ $version->version_number }}? This will create a new version with this content."
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                                >
                                                    Restore
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

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
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-700 focus:bg-blue-700 dark:focus:bg-blue-700 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Update Content
                        </button>
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

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let quill = null;
        
        function initQuill() {
            console.log('initQuill called');
            const editor = document.getElementById('quill-editor');
            console.log('Editor element:', editor);
            console.log('Quill already initialized:', !!quill);
            if (editor && !quill) {
                console.log('Initializing Quill...');
                quill = new Quill(editor, {
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
                    }
                });
                
                // Set initial content
                const content = @this.content_text || '';
                console.log('Setting initial content:', content);
                quill.root.innerHTML = content;
                console.log('Quill initialized successfully');
                
                // Update Livewire property on text change
                quill.on('text-change', function() {
                    @this.set('content_text', quill.root.innerHTML);
                });
            }
        }
        
        // Initialize on page load if rich_text is selected
        console.log('File type:', @this.file_type);
        if (@this.file_type === 'rich_text') {
            console.log('Rich text detected, initializing Quill in 100ms');
            setTimeout(initQuill, 100);
        } else {
            console.log('Not rich text, skipping Quill initialization');
        }
        
        // Re-initialize when file_type changes
        Livewire.on('file-type-changed', () => {
            if (@this.file_type === 'rich_text') {
                quill = null;
                setTimeout(initQuill, 100);
            }
        });
        
        // Sync Quill content before form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            if (quill && @this.file_type === 'rich_text') {
                @this.set('content_text', quill.root.innerHTML);
            }
        });
    });
</script>
@endpush
