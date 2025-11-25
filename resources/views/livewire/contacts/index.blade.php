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
            <a href="{{ route('events.contacts.create', $eventId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Contact
            </a>
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
        <flux:card>
            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company & Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sessions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tags</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                        </thead>

                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <!-- Name -->
                                <td class="px-6 py-4">
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
                                </td>

                                <!-- Company & Title -->
                                <td class="px-6 py-4">
                                    @if($contact->company || $contact->title)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $contact->company }}</div>
                                        @if($contact->title)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->title }}</div>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <!-- Type -->
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $contact->contact_type === 'client' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                        {{ $contact->contact_type === 'producer' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                        {{ $contact->contact_type === 'vendor' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                        {{ $contact->contact_type === 'staff' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                        {{ $contact->contact_type === 'other' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                    ">
                                        {{ ucfirst($contact->contact_type) }}
                                    </span>
                                </td>

                                <!-- Contact Info -->
                                <td class="px-6 py-4">
                                    @if($contact->email)
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $contact->email }}</div>
                                    @endif
                                    @if($contact->phone)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->phone }}</div>
                                    @endif
                                    @if(!$contact->email && !$contact->phone)
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <!-- Sessions -->
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $contact->sessions->count() }}</span>
                                </td>

                                <!-- Tags -->
                                <td class="px-6 py-4">
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
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @if($contact->is_active)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" href="{{ route('events.contacts.show', [$eventId, $contact->id]) }}" square>
                                            <x-lineicon alias="actions.view" />
                                        </flux:button>
                                        <flux:button size="sm" variant="ghost" href="{{ route('events.contacts.edit', [$eventId, $contact->id]) }}" square>
                                            <x-lineicon alias="actions.edit" />
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

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
                        <flux:button href="{{ route('events.contacts.create', $eventId) }}" variant="primary">
                            <flux:icon.plus class="w-5 h-5" />
                            Add Contact
                        </flux:button>
                    </div>
                </div>
            @endif
        </flux:card>
    </div>
</div>
