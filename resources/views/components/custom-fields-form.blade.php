@props(['model', 'eventId', 'modelType'])

@php
    $customFields = \App\Models\CustomField::forEvent($eventId)
        ->forModelType($modelType)
        ->ordered()
        ->get();
@endphp

@if($customFields->count() > 0)
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Custom Fields</h3>
        
        <div class="space-y-4">
            @foreach($customFields as $field)
                <div>
                    <flux:field>
                        <flux:label>
                            {{ $field->name }}
                            @if($field->is_required)
                                <span class="text-red-500">*</span>
                            @endif
                        </flux:label>
                        
                        @php
                            $fieldName = 'customFields.' . $field->id;
                            $currentValue = $model ? $model->getCustomFieldValue($field->id) : null;
                        @endphp
                        
                        @if($field->field_type === 'text')
                            <flux:input 
                                wire:model="{{ $fieldName }}" 
                                type="text"
                                value="{{ $currentValue }}"
                                @if($field->is_required) required @endif
                            />
                        @elseif($field->field_type === 'number')
                            <flux:input 
                                wire:model="{{ $fieldName }}" 
                                type="number"
                                value="{{ $currentValue }}"
                                @if($field->is_required) required @endif
                            />
                        @elseif($field->field_type === 'date')
                            <flux:input 
                                wire:model="{{ $fieldName }}" 
                                type="date"
                                value="{{ $currentValue }}"
                                @if($field->is_required) required @endif
                            />
                        @elseif($field->field_type === 'select')
                            <flux:select 
                                wire:model="{{ $fieldName }}"
                                @if($field->is_required) required @endif
                            >
                                <option value="">Select...</option>
                                @if($field->options)
                                    @foreach($field->options as $option)
                                        <option value="{{ $option }}" @if($currentValue === $option) selected @endif>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                @endif
                            </flux:select>
                        @elseif($field->field_type === 'checkbox')
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.boolean="{{ $fieldName }}"
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Yes</span>
                            </div>
                        @endif
                        
                        @error($fieldName)
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
            @endforeach
        </div>
    </div>
@endif
