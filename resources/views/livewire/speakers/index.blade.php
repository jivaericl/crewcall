<div>
    <flux:header container class="mb-6">
        <flux:heading size="xl">Speakers - {{ $event->name }}</flux:heading>
        
        <flux:button href="{{ route('events.speakers.create', $eventId) }}" icon="plus">
            Add Speaker
        </flux:button>
    </flux:header>

    @if (session()->has('message'))
        <flux:banner variant="success" class="mb-4">
            {{ session('message') }}
        </flux:banner>
    @endif

    <flux:card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search speakers..." icon="magnifying-glass" />
            
            <flux:select wire:model.live="filterTag" placeholder="Filter by tag">
                <option value="">All Tags</option>
                @foreach($allTags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="filterActive">
                <option value="all">All Speakers</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </flux:select>
        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:columns>
                <flux:column>Headshot</flux:column>
                <flux:column>Name</flux:column>
                <flux:column>Title & Company</flux:column>
                <flux:column>Email</flux:column>
                <flux:column>Sessions</flux:column>
                <flux:column>Content</flux:column>
                <flux:column>Tags</flux:column>
                <flux:column>Actions</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse($speakers as $speaker)
                    <flux:row>
                        <flux:cell>
                            @if($speaker->headshot_url)
                                <img src="{{ $speaker->headshot_url }}" alt="{{ $speaker->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400 font-semibold">{{ substr($speaker->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </flux:cell>
                        <flux:cell>
                            <div class="font-semibold">{{ $speaker->name }}</div>
                            @if(!$speaker->is_active)
                                <flux:badge color="gray" size="sm">Inactive</flux:badge>
                            @endif
                            @if($speaker->user_id)
                                <flux:badge color="blue" size="sm">Has Account</flux:badge>
                            @endif
                        </flux:cell>
                        <flux:cell>{{ $speaker->full_title }}</flux:cell>
                        <flux:cell>{{ $speaker->email }}</flux:cell>
                        <flux:cell>
                            <flux:badge color="purple">{{ $speaker->sessions->count() }}</flux:badge>
                        </flux:cell>
                        <flux:cell>
                            <flux:badge color="blue">{{ $speaker->contentFiles->count() }}</flux:badge>
                        </flux:cell>
                        <flux:cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach($speaker->tags->take(3) as $tag)
                                    <flux:badge :style="'background-color: ' . $tag->color . '; color: white;'" size="sm">
                                        {{ $tag->name }}
                                    </flux:badge>
                                @endforeach
                                @if($speaker->tags->count() > 3)
                                    <flux:badge color="gray" size="sm">+{{ $speaker->tags->count() - 3 }}</flux:badge>
                                @endif
                            </div>
                        </flux:cell>
                        <flux:cell>
                            <div class="flex gap-2">
                                <flux:button size="sm" href="{{ route('events.speakers.show', ['eventId' => $eventId, 'speakerId' => $speaker->id]) }}" icon="eye" variant="ghost">
                                    View
                                </flux:button>
                                <flux:button size="sm" href="{{ route('events.speakers.edit', ['eventId' => $eventId, 'speakerId' => $speaker->id]) }}" icon="pencil" variant="ghost">
                                    Edit
                                </flux:button>
                                <flux:button size="sm" wire:click="confirmDelete({{ $speaker->id }})" icon="trash" variant="danger">
                                    Delete
                                </flux:button>
                            </div>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="8">
                            <div class="text-center py-8 text-gray-500">
                                No speakers found. <a href="{{ route('events.speakers.create', $eventId) }}" class="text-blue-600 hover:underline">Add your first speaker</a>
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        <div class="mt-4">
            {{ $speakers->links() }}
        </div>
    </flux:card>

    @if($showDeleteModal)
        <flux:modal wire:model.live="showDeleteModal">
            <flux:heading size="lg">Confirm Delete</flux:heading>
            <flux:text>Are you sure you want to delete this speaker? This action cannot be undone.</flux:text>
            
            <div class="flex gap-2 mt-4">
                <flux:button wire:click="deleteSpeaker" variant="danger">Delete</flux:button>
                <flux:button wire:click="cancelDelete" variant="ghost">Cancel</flux:button>
            </div>
        </flux:modal>
    @endif
</div>
