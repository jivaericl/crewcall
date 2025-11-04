<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Contacts</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage contacts for {{ $event->name }}
                </p>
            </div>
            <flux:button wire:click="$redirect('{{ route('events.contacts.create', $eventId) }}')" variant="primary">
                <flux:icon.plus class="w-5 h-5" />
                Add Contact
            </flux:button>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search contacts..." />
                </div>

                <!-- Type Filter -->
                <div>
                    <flux:select wire:model.live="filterType">
                        <option value="">All Types</option>
                        <option value="client">Client</option>
                        <option value="producer">Producer</option>
                        <option value="vendor">Vendor</option>
                        <option value="staff">Staff</option>
                        <option value="other">Other</option>
                    </flux:select>
                </div>

                <!-- Status Filter -->
                <div>
                    <flux:select wire:model.live="filterStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </flux:select>
                </div>
            </div>

            <!-- Tag Filter -->
            @if($tags->count() > 0)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter by Tags</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <button
                                wire:click="toggleTagFilter({{ $tag->id }})"
                                class="px-3 py-1 rounded-full text-sm font-medium transition-all
                                    {{ in_array($tag->id, $filterTags) ? 'ring-2 ring-offset-2' : 'opacity-60 hover:opacity-100' }}"
                                style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; {{ in_array($tag->id, $filterTags) ? 'ring-color: ' . $tag->color : '' }}"
                            >
                                {{ $tag->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Contacts Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            @if($contacts->count() > 0)
                <flux:table>
                    <flux:columns>
                        <flux:column>Name</flux:column>
                        <flux:column>Company & Title</flux:column>
                        <flux:column>Type</flux:column>
                        <flux:column>Contact Info</flux:column>
                        <flux:column>Sessions</flux:column>
                        <flux:column>Tags</flux:column>
                        <flux:column>Status</flux:column>
                        <flux:column align="end">Actions</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($contacts as $contact)
                            <flux:row wire:key="contact-{{ $contact->id }}">
                                <!-- Name -->
                                <flux:cell>
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span class="text-lg font-medium text-gray-600 dark:text-gray-300">
                                                    {{ substr($contact->first_name, 0, 1) }}{{ substr($contact->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $contact->first_name }} {{ $contact->last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </flux:cell>

                                <!-- Company & Title -->
                                <flux:cell>
                                    @if($contact->company || $contact->title)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $contact->company }}</div>
                                        @if($contact->title)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->title }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </flux:cell>

                                <!-- Type -->
                                <flux:cell>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $contact->type === 'client' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $contact->type === 'producer' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                        {{ $contact->type === 'vendor' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $contact->type === 'staff' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $contact->type === 'other' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                    ">
                                        {{ ucfirst($contact->type) }}
                                    </span>
                                </flux:cell>

                                <!-- Contact Info -->
                                <flux:cell>
                                    @if($contact->email)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $contact->email }}</div>
                                    @endif
                                    @if($contact->phone)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->phone }}</div>
                                    @endif
                                    @if(!$contact->email && !$contact->phone)
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </flux:cell>

                                <!-- Sessions -->
                                <flux:cell>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $contact->sessions->count() }}</span>
                                </flux:cell>

                                <!-- Tags -->
                                <flux:cell>
                                    @if($contact->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($contact->tags->take(3) as $tag)
                                                <span class="px-2 py-1 text-xs rounded-full"
                                                    style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if($contact->tags->count() > 3)
                                                <span class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                                                    +{{ $contact->tags->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </flux:cell>

                                <!-- Status -->
                                <flux:cell>
                                    @if($contact->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </flux:cell>

                                <!-- Actions -->
                                <flux:cell align="end">
                                    <div class="flex items-center gap-2">
                                        <flux:button size="sm" variant="ghost" wire:click="$redirect('{{ route('events.contacts.show', [$eventId, $contact->id]) }}')">
                                            View
                                        </flux:button>
                                        <flux:button size="sm" variant="ghost" wire:click="$redirect('{{ route('events.contacts.edit', [$eventId, $contact->id]) }}')">
                                            Edit
                                        </flux:button>
                                    </div>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $contacts->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No contacts</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new contact.</p>
                    <div class="mt-6">
                        <flux:button wire:click="$redirect('{{ route('events.contacts.create', $eventId) }}')" variant="primary">
                            <flux:icon.plus class="w-5 h-5" />
                            Add Contact
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
