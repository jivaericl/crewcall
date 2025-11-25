<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Speakers - {{ $event->name }}</h2>
        </div>
        <div><flux:button href="{{ route('events.speakers.create', $eventId) }}"><x-lineicon alias="actions.plus" class="mr-1" />Add Speaker</flux:button></div>
    </div>

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
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Headshot</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title & Company</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Content</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tags</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
                </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($speakers as $speaker)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4">
                            @if($speaker->headshot_url)
                                <img src="{{ $speaker->headshot_url }}" alt="{{ $speaker->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400 font-semibold">{{ substr($speaker->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $speaker->name }}</div>
                            @if(!$speaker->is_active)
                                <flux:badge color="gray" size="sm">Inactive</flux:badge>
                            @endif
                            @if($speaker->user_id)
                                <flux:badge color="blue" size="sm">Has Account</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $speaker->full_title }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $speaker->email }}</td>
                        <td class="px-6 py-4">
                            <flux:badge color="purple">{{ $speaker->sessions->count() }}</flux:badge>
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge color="blue">{{ $speaker->contentFiles->count() }}</flux:badge>
                        </td>
                        <td class="px-6 py-4">
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
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <flux:button size="sm" href="{{ route('events.speakers.show', ['eventId' => $eventId, 'speakerId' => $speaker->id]) }}" variant="ghost" square>
                                    <x-lineicon alias="actions.view" />
                                </flux:button>
                                <flux:button size="sm" href="{{ route('events.speakers.edit', ['eventId' => $eventId, 'speakerId' => $speaker->id]) }}" variant="ghost" square>
                                    <x-lineicon alias="actions.edit" />
                                </flux:button>
                                <flux:button size="sm" wire:click="confirmDelete({{ $speaker->id }})" variant="danger" square>
                                    <x-lineicon alias="actions.delete" />
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4">
                            <div class="text-center py-8 text-gray-500">
                                No speakers found. <a href="{{ route('events.speakers.create', $eventId) }}" class="text-blue-600 hover:underline">Add your first speaker</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

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
</div>