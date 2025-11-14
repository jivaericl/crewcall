<div wire:poll.5s="refreshMessages" class="flex bg-gray-50 dark:bg-gray-900" style="height: calc(100vh - 4rem);">
    <!-- Conversations Sidebar -->
    <div class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Conversations</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conv)
                <button 
                    wire:click="switchConversation('{{ $conv['type'] }}', {{ $conv['id'] }})"
                    class="w-full p-4 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 text-left transition-colors {{ $conv['type'] === 'dm' && $conv['id'] == $userId ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                >
                    <div class="w-12 h-12 rounded-full {{ $conv['type'] === 'event' ? 'bg-green-500' : 'bg-blue-500' }} flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ $conv['avatar'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $conv['name'] }}</h3>
                            @if($conv['unread_count'] > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $conv['unread_count'] }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($conv['last_message'], 40) }}</p>
                        @if($conv['last_message_at'])
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $conv['last_message_at']->diffForHumans() }}</p>
                        @endif
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-sm">No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Main DM Area -->
    <div class="flex-1 flex flex-col">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('chat.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white text-lg font-semibold">
                    {{ substr($recipient->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $recipient->name }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Direct Message</p>
                </div>
            </div>
            <button 
                wire:click="$dispatch('showUserProfile', { userId: {{ $recipient->id }} })"
                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                title="View Profile"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Messages Area -->
    <div id="dm-messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
        @forelse($messages as $msg)
            <div class="flex {{ $msg['user_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%]">
                    <!-- Message Header -->
                    @if($msg['user_id'] !== auth()->id())
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr($msg['user']['name'], 0, 1) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $msg['user']['name'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($msg['created_at'])->format('g:i A') }}</span>
                        </div>
                    @endif

                    <!-- Message Content -->
                    <div class="{{ $msg['user_id'] === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg px-4 py-3 shadow-sm">
                        <p class="text-sm break-words whitespace-pre-wrap">{{ $msg['message'] }}</p>
                        
                        @if($msg['user_id'] === auth()->id())
                            <p class="text-xs text-blue-100 mt-1">{{ \Carbon\Carbon::parse($msg['created_at'])->format('g:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-lg font-medium">No messages yet</p>
                <p class="text-sm">Start a conversation with {{ $recipient->name }}</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input -->
    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4">
        <form wire:submit="sendMessage" class="flex gap-2">
            <input 
                type="text" 
                wire:model="newMessage"
                placeholder="Type a message..."
                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                wire:keydown.enter.prevent="sendMessage"
            >
            <button 
                type="submit"
                class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!newMessage.trim()"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto-scroll to bottom on load and new messages
    document.addEventListener('livewire:initialized', () => {
        const container = document.getElementById('dm-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    Livewire.on('message-sent', () => {
        setTimeout(() => {
            const container = document.getElementById('dm-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });
</script>
    </div>
</div>
