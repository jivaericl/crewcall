<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <flux:header>
        <flux:heading size="xl">Activity Feed</flux:heading>
        <flux:subheading>All mentions and notifications</flux:subheading>
    </flux:header>

    @if (session()->has('success'))
        <flux:alert variant="success" class="mb-4">
            {{ session('success') }}
        </flux:alert>
    @endif

    <!-- Filters and Actions -->
    <div class="flex items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
            <flux:select wire:model.live="filter" label="Filter">
                <option value="all">All Mentions ({{ $mentions->total() }})</option>
                <option value="unread">Unread ({{ $unreadCount }})</option>
                <option value="read">Read</option>
            </flux:select>

            <flux:select wire:model.live="days" label="Time Range">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="0">All time</option>
            </flux:select>
        </div>

        @if ($unreadCount > 0)
            <flux:button wire:click="markAllAsRead" variant="primary">
                Mark All as Read
            </flux:button>
        @endif
    </div>

    <!-- Mentions List -->
    <div class="space-y-3">
        @forelse ($mentions as $mention)
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border {{ $mention->is_read ? 'border-gray-200 dark:border-gray-700' : 'border-blue-500 dark:border-blue-400' }}">
                <div class="flex items-start gap-4">
                    <!-- User Avatar -->
                    <div class="w-12 h-12 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ strtoupper(substr($mention->comment->user->name, 0, 1)) }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-semibold">{{ $mention->comment->user->name }}</span>
                                    mentioned you in
                                    <span class="font-medium">{{ $mention->comment->commentable_type_name }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $mention->created_at->diffForHumans() }}
                                    @if ($mention->is_read)
                                        · Read {{ $mention->read_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>

                            @if (!$mention->is_read)
                                <flux:badge color="blue">New</flux:badge>
                            @endif
                        </div>

                        <!-- Comment Preview -->
                        <div class="bg-gray-50 dark:bg-gray-900 rounded p-3 mb-3">
                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                                {!! nl2br($mention->comment->formatted_comment) !!}
                            </p>
                        </div>

                        <!-- Context -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <span>Event: {{ $mention->comment->event->name }}</span>
                            @if ($mention->comment->commentable)
                                <span>·</span>
                                <span>{{ $mention->comment->commentable_type_name }}: {{ $mention->comment->commentable_name }}</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <a href="{{ $mention->comment->getActionUrl() }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition">
                                <x-lineicon alias="ui.view-comment" size="w-4 h-4" />
                                View Comment
                            </a>

                            @if (!$mention->is_read)
                                <flux:button 
                                    wire:click="markAsRead({{ $mention->id }})" 
                                    variant="ghost" 
                                    size="sm"
                                >
                                    Mark as Read
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-500 mb-2">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">
                    @if ($filter === 'unread')
                        No unread mentions
                    @else
                        No mentions found
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($mentions->hasPages())
        <div class="mt-6">
            {{ $mentions->links() }}
        </div>
    @endif
    </div>
</div>