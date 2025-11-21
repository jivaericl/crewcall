<div wire:poll.5s="refreshChat" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <button 
        wire:click="toggleChat"
        class="relative bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-full p-4 shadow-lg transition-all duration-200"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Chat Window -->
    @if($isOpen)
        <div class="absolute bottom-16 right-0 w-96 h-[500px] bg-white dark:bg-gray-800 rounded-lg shadow-2xl flex flex-col border border-gray-200 dark:border-gray-700">
            <!-- Header -->
            <div class="bg-blue-600 dark:bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center">
                <div>
                    <h3 class="font-semibold">Team Chat</h3>
                    @if($eventId)
                        <p class="text-xs text-blue-100">{{ count($onlineUsers) }} online</p>
                    @else
                        <p class="text-xs text-blue-100">No event selected</p>
                    @endif
                </div>
                <button wire:click="toggleChat" class="hover:bg-blue-700 dark:hover:bg-blue-600 rounded p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Messages -->
            <div id="widget-messages-container" class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($messages as $msg)
                    <div class="flex {{ $msg['user_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%]">
                            @if($msg['user_id'] !== auth()->id())
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 cursor-pointer hover:underline" wire:click="$dispatch('showUserProfile', { userId: {{ $msg['user']['id'] }}, eventId: @js($eventId) })">{{ $msg['user']['name'] }}</p>
                            @endif
                            <div class="{{ $msg['user_id'] === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg px-3 py-2">
                                <p class="text-sm break-words">{{ $msg['message'] }}</p>
                                <p class="text-xs {{ $msg['user_id'] === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }} mt-1">
                                    {{ \Carbon\Carbon::parse($msg['created_at'])->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-sm">No messages yet</p>
                        <p class="text-xs">Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Input -->
            <form wire:submit="sendMessage" class="border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex gap-2">
                    <input 
                        type="text"
                        wire:model="message"
                        placeholder="{{ $eventId ? 'Type a message...' : 'Select an event to chat' }}"
                        {{ !$eventId ? 'disabled' : '' }}
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                    <button 
                        type="submit"
                        {{ !$eventId ? 'disabled' : '' }}
                        class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg px-4 py-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
                @error('message') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </form>
        </div>
    @endif
</div>

@script
<script>
    // Auto-scroll to bottom when chat opens or new message arrives
    $wire.on('chat-opened', () => {
        setTimeout(() => {
            const container = document.getElementById('widget-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });

    $wire.on('message-sent', () => {
        setTimeout(() => {
            const container = document.getElementById('widget-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });

    // Browser notification support
    let notificationPermission = 'default';
    
    // Request notification permission on page load
    if ('Notification' in window) {
        notificationPermission = Notification.permission;
        
        if (notificationPermission === 'default') {
            // Will request permission when user interacts with chat
        }
    }

    // Listen for new messages to show notifications
    $wire.on('new-message-notification', (event) => {
        const data = event[0] || event;
        
        // Only show notification if chat is closed and permission is granted
        if (notificationPermission === 'granted' && !$wire.isOpen) {
            const notification = new Notification('New Message from ' + data.userName, {
                body: data.message,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: 'chat-message',
                requireInteraction: false,
            });

            notification.onclick = function() {
                window.focus();
                $wire.toggleChat();
                notification.close();
            };

            // Auto-close after 5 seconds
            setTimeout(() => notification.close(), 5000);
        }

        // Play sound if enabled
        if (data.soundEnabled) {
            playNotificationSound();
        }
    });

    // Request notification permission when chat opens
    $wire.on('chat-opened', () => {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                notificationPermission = permission;
            });
        }
    });

    // Simple notification sound
    function playNotificationSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Could not play notification sound:', e);
        }
    }
</script>
@endscript
