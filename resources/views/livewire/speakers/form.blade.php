<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $speakerId ? 'Edit' : 'Add' }} Speaker - {{ $event->name }}</h2>
        </div>
        
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model.blur="first_name" label="First Name *" placeholder="John" />
                    <flux:input wire:model.blur="last_name" label="Last Name *" placeholder="Doe" />
                    <flux:input wire:model.blur="title" label="Title" placeholder="CEO" />
                    
                    <div>
                        <flux:field>
                            <flux:label>Company</flux:label>
                            <flux:input 
                                type="text"
                                wire:model.blur="company" 
                                list="companies-list"
                                placeholder="Acme Corp"
                            />
                            <datalist id="companies-list">
                                @foreach($companies as $comp)
                                    <option value="{{ $comp }}">
                                @endforeach
                            </datalist>
                        </flux:field>
                    </div>
                    
                    <flux:input wire:model.blur="email" label="Email" type="email" placeholder="john@acme.com" />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Biography & Notes</flux:heading>
                
                <div class="space-y-4">
                    <flux:textarea wire:model="bio" label="Biography" rows="6" placeholder="Speaker biography..." />
                    <flux:textarea wire:model="notes" label="Internal Notes" rows="4" placeholder="Internal notes about this speaker..." />
                    
                    <div>
                        <flux:field>
                            <flux:label>Contact Person</flux:label>
                            <flux:input 
                                type="text"
                                wire:model.blur="contact_person" 
                                list="contact-persons-list"
                                placeholder="Jane Smith"
                            />
                            <datalist id="contact-persons-list">
                                @foreach($contactPersons as $person)
                                    <option value="{{ $person }}">
                                @endforeach
                            </datalist>
                            <flux:description>Start typing to see suggestions from event contacts</flux:description>
                        </flux:field>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Headshot</flux:heading>
                
                @if($existingHeadshot)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $existingHeadshot) }}" alt="Current headshot" class="w-32 h-32 rounded-full object-cover">
                    </div>
                @endif
                
                <flux:input type="file" wire:model="headshot" label="Upload Headshot" accept="image/*" />
                
                @if($headshot)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Preview:</p>
                        <img src="{{ $headshot->temporaryUrl() }}" class="w-32 h-32 rounded-full object-cover mt-2">
                    </div>
                @endif
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Tags</flux:heading>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($allTags as $tag)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:model="selectedTags" 
                                value="{{ $tag->id }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="text-sm font-medium" style="color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Session Assignments</flux:heading>
                
                <div class="space-y-2">
                    @forelse($sessions as $session)
                        <flux:checkbox wire:model="selectedSessions" value="{{ $session->id }}">
                            {{ $session->name }} 
                            @if($session->start_date)
                                ({{ $session->start_date->format('M d, Y g:i A') }})
                            @endif
                        </flux:checkbox>
                    @empty
                        <p class="text-gray-500">No sessions available for this event.</p>
                    @endforelse
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Content Assignments</flux:heading>
                
                <div class="space-y-2">
                    @forelse($contentFiles as $file)
                        <flux:checkbox wire:model="selectedContent" value="{{ $file->id }}">
                            {{ $file->name }} ({{ $file->file_type }})
                        </flux:checkbox>
                    @empty
                        <p class="text-gray-500">No content files available for this event.</p>
                    @endforelse
                </div>
            </flux:card>

            @if(!$speakerId)
            <flux:card>
                <flux:heading size="lg" class="mb-4">User Account Creation</flux:heading>
                
                <div class="space-y-4">
                    <flux:checkbox wire:model.live="createUser">
                        Create user account for this speaker
                    </flux:checkbox>

                    @if($createUser)
                        <div class="ml-6 space-y-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                            <flux:select wire:model="userRole" label="Account Type">
                                <option value="viewer">Viewer (Read-only)</option>
                                <option value="user">User (Full access)</option>
                            </flux:select>

                            <flux:checkbox wire:model.live="autoGeneratePassword">
                                Auto-generate secure password
                            </flux:checkbox>

                            @if(!$autoGeneratePassword)
                                <flux:input wire:model="customPassword" type="password" label="Password" />
                            @endif

                            <flux:checkbox wire:model="sendWelcomeEmail">
                                Send welcome email with credentials
                            </flux:checkbox>
                        </div>
                    @endif
                </div>
            </flux:card>
            @endif
            
            @if($customFieldsList->count() > 0)
            <flux:card>
                <flux:heading size="lg" class="mb-4">Custom Fields</flux:heading>
                
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
            </flux:card>
            @endif

            <flux:card>
                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="is_active">
                        Active
                    </flux:checkbox>

                    <div class="flex gap-2">
                        <flux:button type="submit" variant="primary">
                            {{ $speakerId ? 'Update' : 'Create' }} Speaker
                        </flux:button>
                        <flux:button href="{{ route('events.speakers.index', $eventId) }}" variant="ghost">
                            Cancel
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        </div>
    </form>
    </div>
</div>