<?php

namespace App\Livewire\Chat;

use App\Models\ChatMessage;
use App\Models\UserPresence;
use Livewire\Component;
use Livewire\Attributes\On;

class Widget extends Component
{
    public $isOpen = false;
    public $message = '';
    public $messages = [];
    public $onlineUsers = [];
    public $unreadCount = 0;
    public $eventId;

    public function mount()
    {
        // Try to get event ID from multiple sources
        $this->eventId = session('current_event_id') 
            ?? request()->route('eventId') 
            ?? request()->get('event_id');
        
        // If still no event, try to get the first event the user is assigned to
        if (!$this->eventId && auth()->check()) {
            $event = auth()->user()->events()->first();
            if ($event) {
                $this->eventId = $event->id;
            }
        }
        
        if ($this->eventId) {
            $this->loadMessages();
            $this->updatePresence();
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->unreadCount = 0;
            $this->loadMessages();
            $this->dispatch('chat-opened');
        }
    }

    public function loadMessages()
    {
        if (!$this->eventId) return;

        $this->messages = ChatMessage::with('user')
            ->forEvent($this->eventId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        $this->loadOnlineUsers();
        
        // Update unread count if widget is closed
        if (!$this->isOpen) {
            $this->updateUnreadCount();
        }
    }

    public function loadOnlineUsers()
    {
        if (!$this->eventId) return;

        $this->onlineUsers = UserPresence::with('user')
            ->where('event_id', $this->eventId)
            ->where('status', 'online')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->get();
    }

    public function sendMessage()
    {
        if (!$this->eventId) {
            $this->addError('message', 'No event selected');
            return;
        }

        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'event_id' => $this->eventId,
            'user_id' => auth()->id(),
            'message' => $this->message,
            'message_type' => 'message',
        ]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    #[On('refresh-chat')]
    public function refreshChat()
    {
        $previousCount = count($this->messages);
        $this->loadMessages();
        
        // Check for new messages and trigger notification
        if (count($this->messages) > $previousCount && !$this->isOpen) {
            $latestMessage = end($this->messages);
            if ($latestMessage && $latestMessage->user_id !== auth()->id()) {
                $this->triggerNotification($latestMessage);
            }
        }
    }

    protected function triggerNotification($message)
    {
        if (!$this->eventId) return;

        $event = \App\Models\Event::find($this->eventId);
        if (!$event) return;

        $this->dispatch('new-message-notification', [
            'userName' => $message->user->name,
            'message' => \Illuminate\Support\Str::limit($message->message, 100),
            'soundEnabled' => $event->chat_sound_enabled ?? true,
        ]);
    }

    public function updatePresence()
    {
        if (!$this->eventId) return;

        UserPresence::updateOrCreate(
            [
                'event_id' => $this->eventId,
                'user_id' => auth()->id(),
            ],
            [
                'status' => 'online',
                'last_seen_at' => now(),
                'current_page' => request()->path(),
            ]
        );
    }

    public function updateUnreadCount()
    {
        if (!$this->eventId) return;

        // Get last read timestamp from user presence
        $lastRead = UserPresence::where('event_id', $this->eventId)
            ->where('user_id', auth()->id())
            ->value('last_seen_at');

        if (!$lastRead) {
            $lastRead = now()->subDays(1);
        }

        $this->unreadCount = ChatMessage::forEvent($this->eventId)
            ->where('user_id', '!=', auth()->id())
            ->where('created_at', '>', $lastRead)
            ->count();
    }

    public function render()
    {
        return view('livewire.chat.widget');
    }
}
