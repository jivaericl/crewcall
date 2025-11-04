<div>
    <flux:header container class="mb-6">
        <flux:heading size="xl">{{ $speakerId ? 'Edit' : 'Add' }} Speaker - {{ $event->name }}</flux:heading>
    </flux:header>

    <form wire:submit="save">
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model.blur="first_name" label="First Name *" placeholder="John" />
                    <flux:input wire:model.blur="last_name" label="Last Name *" placeholder="Doe" />
                    <flux:input wire:model.blur="title" label="Title" placeholder="CEO" />
                    
                    <div>
                        <flux:label>Company</flux:label>
                        <input 
                            type="text" 
                            wire:model.blur="company" 
                            list="companies-list"
                            placeholder="Acme Corp"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800"
                        />
                        <datalist id="companies-list">
                            @foreach($companies as $comp)
                                <option value="{{ $comp }}">
                            @endforeach
                        </datalist>
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
                        <flux:label>Contact Person</flux:label>
                        <input 
                            type="text" 
                            wire:model.blur="contact_person" 
                            list="contact-persons-list"
                            placeholder="Jane Smith"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800"
                        />
                        <datalist id="contact-persons-list">
                            @foreach($contactPersons as $person)
                                <option value="{{ $person }}">
                            @endforeach
                        </datalist>
                        <flux:description>Start typing to see suggestions from event contacts</flux:description>
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
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach($allTags as $tag)
                        <flux:checkbox wire:model="selectedTags" value="{{ $tag->id }}">
                            <span :style="'color: ' + '{{ $tag->color }}'">{{ $tag->name }}</span>
                        </flux:checkbox>
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
