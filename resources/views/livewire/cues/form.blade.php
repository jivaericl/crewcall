<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $cueId ? 'Edit Cue' : 'Create Cue' }} - {{ $segment->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <form wire:submit.prevent="save" class="p-6 space-y-6">
                    <!-- Cue Name -->
                    <div>
                        <flux:label for="name" required>Cue Name</flux:label>
                        <flux:input 
                            wire:model.live="name" 
                            id="name" 
                            type="text" 
                            placeholder="e.g., Lights Up, Play Video, Speaker Intro"
                            class="w-full"
                        />
                        @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Code and Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:label for="code">Cue Code</flux:label>
                            <flux:input 
                                wire:model="code" 
                                id="code" 
                                type="text" 
                                placeholder="e.g., LX-01, AUD-01"
                                class="w-full"
                            />
                            <flux:description>Optional identifier</flux:description>
                            @error('code') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:label for="cue_type_id" required>Cue Type</flux:label>
                            <flux:select wire:model="cue_type_id" id="cue_type_id" class="w-full">
                                <option value="">Select type...</option>
                                @foreach($cueTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('cue_type_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <flux:label for="description">Description</flux:label>
                        <flux:textarea 
                            wire:model="description" 
                            id="description" 
                            rows="3"
                            placeholder="Detailed description of the cue..."
                            class="w-full"
                        />
                        @error('description') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Time, Status, Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <flux:label for="time">Time</flux:label>
                            <flux:input 
                                wire:model="time" 
                                id="time" 
                                type="time" 
                                class="w-full"
                            />
                            <flux:description>Cue execution time</flux:description>
                            @error('time') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:label for="priority" required>Priority</flux:label>
                            <flux:select wire:model="priority" id="priority" class="w-full">
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </flux:select>
                            @error('priority') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Content File and Operator -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <flux:label for="content_file_id">Content File</flux:label>
                            <flux:select wire:model="content_file_id" id="content_file_id" class="w-full">
                                <option value="">Select content file...</option>
                                @foreach($contentFiles as $file)
                                    <option value="{{ $file->id }}">
                                        {{ $file->file_type_icon }} {{ $file->name }}
                                        @if($file->category)
                                            ({{ $file->category->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:description>Select from content library</flux:description>
                            @error('content_file_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:label for="operator_id">Operator</flux:label>
                            <flux:select wire:model="operator_id" id="operator_id" class="w-full">
                                <option value="">Select operator...</option>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:description>Assigned team member</flux:description>
                            @error('operator_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <flux:label for="notes">Notes</flux:label>
                        <flux:textarea 
                            wire:model="notes" 
                            id="notes" 
                            rows="3"
                            placeholder="Additional notes or instructions..."
                            class="w-full"
                        />
                        @error('notes') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <!-- Tags -->
                    <div>
                        <flux:label>Tags</flux:label>
                        <flux:description class="mb-3">Select tags to categorize this cue (max 10)</flux:description>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($allTags as $tag)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ in_array($tag->id, $selectedTags) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedTags" 
                                        value="{{ $tag->id }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        {{ count($selectedTags) >= 10 && !in_array($tag->id, $selectedTags) ? 'disabled' : '' }}
                                    >
                                    <span class="ml-2 text-sm font-medium" style="color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($selectedTags) >= 10)
                            <p class="mt-2 text-sm text-amber-600 dark:text-amber-400">Maximum of 10 tags reached</p>
                        @endif
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

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <flux:button href="{{ route('segments.cues.index', $segmentId) }}" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $cueId ? 'Update Cue' : 'Create Cue' }}
                        </flux:button>
                    </div>
                </form>
            </div>

            <!-- Comment Section -->
            @if($cueId)
                @php
                    $cue = \App\Models\Cue::with('segment.session.event')->find($cueId);
                @endphp
                @if($cue && $cue->segment && $cue->segment->session && $cue->segment->session->event)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-8 p-8">
                        @livewire('comments.comment-section', [
                            'commentable' => $cue,
                            'eventId' => $cue->segment->session->event_id
                        ])
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
