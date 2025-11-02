<div>
    <flux:header container class="mb-6">
        <flux:heading size="xl">{{ $speakerId ? 'Edit' : 'Add' }} Speaker - {{ $event->name }}</flux:heading>
    </flux:header>

    <form wire:submit="save">
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="name" label="Name *" placeholder="John Doe" />
                    <flux:input wire:model="title" label="Title" placeholder="CEO" />
                    <flux:input wire:model="company" label="Company" placeholder="Acme Corp" />
                    <flux:input wire:model="email" label="Email" type="email" placeholder="john@acme.com" />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg" class="mb-4">Biography & Notes</flux:heading>
                
                <div class="space-y-4">
                    <flux:textarea wire:model="bio" label="Biography" rows="6" placeholder="Speaker biography..." />
                    <flux:textarea wire:model="notes" label="Internal Notes" rows="4" placeholder="Internal notes about this speaker..." />
                    <flux:input wire:model="contact_person" label="Contact Person" placeholder="Jane Smith" />
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
                            {{ $session->name }} ({{ $session->start_time }})
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
