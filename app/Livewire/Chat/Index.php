<?php

namespace App\Livewire\Chat;

use App\Models\ChatMessage;
use App\Models\UserPresence;
use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $message = '';
    public $searchTerm = '';
    public $filterType = 'all';
    public $eventId;
    public $event;
    public $onlineUsers = [];
    public $typingUsers = [];

    public function mount()
    {
        $this->eventId = session('current_event_id');
        if ($this->eventId) {
            $this->event = Event::find($this->eventId);
            $this->loadOnlineUsers();
            $this->updatePresence();
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
            'message' => 'required|string|max:2000',
        ]);

        ChatMessage::create([
            'event_id' => $this->eventId,
            'user_id' => auth()->id(),
            'message' => $this->message,
            'message_type' => 'message',
        ]);

        $this->message = '';
        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
    }

    public function deleteMessage($messageId)
    {
        $message = ChatMessage::find($messageId);
        
        if ($message && ($message->user_id === auth()->id() || auth()->user()->is_admin)) {
            $message->delete();
        }
    }

    public function pinMessage($messageId)
    {
        $message = ChatMessage::find($messageId);
        
        if ($message && auth()->user()->is_admin) {
            $message->update(['is_pinned' => !$message->is_pinned]);
        }
    }

    #[On('refresh-chat')]
    public function refreshChat()
    {
        $this->loadOnlineUsers();
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
                'current_page' => 'chat',
            ]
        );
    }

    public function getMessagesProperty()
    {
        if (!$this->eventId) return collect();

        $query = ChatMessage::with('user')
            ->forEvent($this->eventId);

        if ($this->searchTerm) {
            $query->where('message', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->filterType !== 'all') {
            $query->byType($this->filterType);
        }

        return $query->orderBy('created_at', 'asc')
            ->get();
    }

    public function getPinnedMessagesProperty()
    {
        if (!$this->eventId) return collect();

        return ChatMessage::with('user')
            ->forEvent($this->eventId)
            ->pinned()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.chat.index', [
            'messages' => $this->messages,
            'pinnedMessages' => $this->pinnedMessages,
        ]);
    }
}
