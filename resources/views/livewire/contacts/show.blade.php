<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header with Actions -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $contact->first_name }} {{ $contact->last_name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $contact->company }} @if($contact->title) - {{ $contact->title }} @endif
                </p>
            </div>
            <div class="flex gap-3">
                <flux:button variant="ghost" wire:click="toggleActive">
                    {{ $contact->is_active ? 'Deactivate' : 'Activate' }}
                </flux:button>
                <flux:button variant="ghost" href="{{ route('events.contacts.edit', [$eventId, $contactId]) }}">
                    Edit
                </flux:button>
                <flux:button variant="danger" wire:click="delete" wire:confirm="Are you sure you want to delete this contact?">
                    Delete
                </flux:button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                    
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $contact->type === 'client' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $contact->type === 'producer' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                    {{ $contact->type === 'vendor' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $contact->type === 'staff' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $contact->type === 'other' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}
                                ">
                                    {{ ucfirst($contact->type) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
                                @if($contact->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>

                        @if($contact->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <a href="mailto:{{ $contact->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $contact->email }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($contact->phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <a href="tel:{{ $contact->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        {{ $contact->phone }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if($contact->address || $contact->city || $contact->state || $contact->zip || $contact->country)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if($contact->address) {{ $contact->address }}<br> @endif
                                    @if($contact->city || $contact->state || $contact->zip)
                                        {{ $contact->city }}@if($contact->city && $contact->state),@endif {{ $contact->state }} {{ $contact->zip }}<br>
                                    @endif
                                    @if($contact->country) {{ $contact->country }} @endif
                                </dd>
                            </div>
                        @endif
                    </dl>

                    @if($contact->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</dt>
                            <dd class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $contact->notes }}</dd>
                        </div>
                    @endif
                </div>

                <!-- Sessions -->
                @if($contact->sessions->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sessions ({{ $contact->sessions->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($contact->sessions as $session)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $session->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $session->start_date->format('M d, Y g:i A') }}
                                        </div>
                                    </div>
                                    <flux:button size="sm" variant="ghost" href="{{ route('events.sessions.edit', [$eventId, $session->id]) }}">
                                        View
                                    </flux:button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Content Files -->
                @if($contact->contentFiles->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Files ({{ $contact->contentFiles->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($contact->contentFiles as $file)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $file->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $file->category->name ?? 'Uncategorized' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Comments -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comments</h3>
                    @livewire('comments.comment-section', [
                        'commentable' => $contact,
                        'eventId' => $eventId
                    ])
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Tags -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h3>
                    @if($contact->tags->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($contact->tags as $tag)
                                <span class="px-3 py-1 rounded-full text-sm font-medium"
                                    style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No tags assigned</p>
                    @endif
                </div>

                <!-- Metadata -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Metadata</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->created_at->format('M d, Y g:i A') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $contact->updated_at->format('M d, Y g:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
