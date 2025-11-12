<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fieldId ? 'Edit Custom Field' : 'Create Custom Field' }}</h2>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form wire:submit.prevent="save">
                        <!-- Model Type -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Model Type *</flux:label>
                                <flux:select wire:model.live="model_type" required>
                                    @foreach($modelTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                @error('model_type') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                                <flux:description>Select which model this custom field applies to</flux:description>
                            </flux:field>
                        </div>

                        <!-- Field Name -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Field Name *</flux:label>
                                <flux:input 
                                    wire:model.blur="name" 
                                    type="text" 
                                    placeholder="e.g., Continuing Education Credits"
                                    required
                                />
                                @error('name') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                                <flux:description>A descriptive name for this custom field</flux:description>
                            </flux:field>
                        </div>

                        <!-- Field Type -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Field Type *</flux:label>
                                <flux:select wire:model.live="field_type" required>
                                    @foreach($fieldTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                @error('field_type') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                                <flux:description>
                                    @if($field_type === 'text') Text input for short answers
                                    @elseif($field_type === 'number') Numeric input for quantities or values
                                    @elseif($field_type === 'date') Date picker for date values
                                    @elseif($field_type === 'select') Dropdown with predefined options
                                    @elseif($field_type === 'checkbox') Yes/No checkbox
                                    @endif
                                </flux:description>
                            </flux:field>
                        </div>

                        <!-- Options (for select type) -->
                        @if($field_type === 'select')
                            <div class="mb-6">
                                <flux:field>
                                    <flux:label>Options *</flux:label>
                                    <flux:textarea 
                                        wire:model.blur="options" 
                                        rows="5"
                                        placeholder="Enter one option per line&#10;Option 1&#10;Option 2&#10;Option 3"
                                        required
                                    />
                                    @error('options') 
                                        <flux:error>{{ $message }}</flux:error>
                                    @enderror
                                    <flux:description>Enter each option on a new line</flux:description>
                                </flux:field>
                            </div>
                        @endif

                        <!-- Is Required -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="is_required"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Make this field required
                                </span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Required fields must be filled out when creating or editing sessions
                            </p>
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-6">
                            <flux:field>
                                <flux:label>Sort Order</flux:label>
                                <flux:input 
                                    wire:model.blur="sort_order" 
                                    type="number" 
                                    min="0"
                                />
                                @error('sort_order') 
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror>
                                <flux:description>Lower numbers appear first (0 = first)</flux:description>
                            </flux:field>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <flux:button 
                                href="{{ route('custom-fields.index', $eventId) }}" 
                                variant="ghost"
                                type="button"
                            >
                                Cancel
                            </flux:button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $fieldId ? 'Update Field' : 'Create Field' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
