<div wire:poll.3s="refreshChat" class="flex flex-col bg-gray-50 dark:bg-gray-900" style="height: calc(100vh - 4rem);">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Team Chat</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ count($onlineUsers) }} {{ Str::plural('person', count($onlineUsers)) }} online
                </p>
            </div>
            
            <!-- Search and Filter -->
            <div class="flex gap-3 items-center">
                <a href="{{ route('chat.settings') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" title="Chat Settings">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </a>
                <input 
                    type="text"
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search messages..."
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                <select 
                    wire:model.live="filterType"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="all">All Messages</option>
                    <option value="message">Messages</option>
                    <option value="announcement">Announcements</option>
                    <option value="system">System</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Pinned Messages -->
            @if(count($pinnedMessages) > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800 p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-300 mb-1">Pinned Messages</p>
                            @foreach($pinnedMessages as $pinned)
                                <div class="text-sm text-yellow-700 dark:text-yellow-200 mb-1">
                                    <span class="font-medium">{{ $pinned->user->name }}:</span> {{ Str::limit($pinned->message, 100) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Messages Area -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($messages as $msg)
                    <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}" id="message-{{ $msg->id }}">
                        <div class="max-w-[70%] group">
                            <!-- Message Header -->
                            @if($msg->user_id !== auth()->id())
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($msg->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer hover:underline" wire:click="$dispatch('showUserProfile', { userId: {{ $msg->user->id }} })">{{ $msg->user->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $msg->created_at->format('g:i A') }}</span>
                                </div>
                            @endif

                            <!-- Message Content -->
                            <div class="relative">
                                <div class="{{ $msg->user_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg px-4 py-3 shadow-sm {{ $msg->is_pinned ? 'ring-2 ring-yellow-400' : '' }}">
                                    @if($msg->is_pinned)
                                        <div class="flex items-center gap-1 text-xs {{ $msg->user_id === auth()->id() ? 'text-blue-100' : 'text-yellow-600 dark:text-yellow-400' }} mb-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                            </svg>
                                            <span>Pinned</span>
                                        </div>
                                    @endif
                                    
                                    <p class="text-sm break-words whitespace-pre-wrap">{{ $msg->message }}</p>
                                    
                                    @if($msg->user_id === auth()->id())
                                        <p class="text-xs text-blue-100 mt-1">{{ $msg->created_at->format('g:i A') }}</p>
                                    @endif
                                </div>

                                <!-- Message Actions -->
                                <div class="absolute -right-2 top-0 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                    @if(auth()->user()->is_admin)
                                        <button 
                                            wire:click="pinMessage({{ $msg->id }})"
                                            class="bg-white dark:bg-gray-600 rounded-full p-1.5 shadow-md hover:bg-gray-100 dark:hover:bg-gray-500"
                                            title="{{ $msg->is_pinned ? 'Unpin' : 'Pin' }} message"
                                        >
                                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    @if($msg->user_id === auth()->id() || auth()->user()->is_admin)
                                        <button 
                                            wire:click="deleteMessage({{ $msg->id }})"
                                            wire:confirm="Are you sure you want to delete this message?"
                                            class="bg-white dark:bg-gray-600 rounded-full p-1.5 shadow-md hover:bg-red-50 dark:hover:bg-red-900"
                                            title="Delete message"
                                        >
                                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-lg font-medium">No messages yet</p>
                            <p class="text-sm">Be the first to start the conversation!</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <form wire:submit="sendMessage" class="flex gap-3">
                    <textarea 
                        wire:model="message"
                        placeholder="Type your message..."
                        rows="2"
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 resize-none"
                        wire:keydown.ctrl.enter="sendMessage"
                    ></textarea>
                    <button 
                        type="submit"
                        class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg px-6 py-2 transition-colors self-end"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
                @error('message')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Press Ctrl+Enter to send</p>
            </div>
        </div>

        <!-- Online Users Sidebar -->
        <div class="w-64 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Online Now ({{ count($onlineUsers) }})</h3>
            <div class="space-y-2">
                @foreach($onlineUsers as $presence)
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                {{ substr($presence['user']['name'], 0, 1) }}
                            </div>
                            @php
                                $lastSeen = \Carbon\Carbon::parse($presence['last_seen_at'] ?? $presence['updated_at']);
                                $isOnline = $lastSeen->gt(now()->subMinutes(5));
                            @endphp
                            @if($isOnline)
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                            @else
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 rounded-full border-2 border-white dark:border-gray-800"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate cursor-pointer hover:underline" wire:click="$dispatch('showUserProfile', { userId: {{ $presence['user']['id'] }} })">{{ $presence['user']['name'] }}</p>
                            @if($isOnline)
                                <p class="text-xs text-green-500 dark:text-green-400">● Online now</p>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400">Last seen {{ $lastSeen->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Auto-scroll to bottom on new messages
    $wire.on('scroll-to-bottom', () => {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    // Scroll to bottom on page load
    document.addEventListener('livewire:navigated', () => {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });
</script>
@endscript
