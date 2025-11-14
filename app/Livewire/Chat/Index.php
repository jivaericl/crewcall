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
    public $conversations = [];
    public $activeConversationType = 'event'; // 'event' or 'dm'
    public $activeConversationId = null;

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
            $this->event = Event::find($this->eventId);
            $this->activeConversationType = 'event';
            $this->activeConversationId = $this->eventId;
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
            ->get()
            ->toArray();
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

    public function loadConversations()
    {
        $conversations = [];
        
        // Get user's events (team chats)
        $events = auth()->user()->events()->get();
        foreach ($events as $event) {
            $lastMessage = ChatMessage::where('event_id', $event->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $conversations[] = [
                'type' => 'event',
                'id' => $event->id,
                'name' => $event->name,
                'avatar' => substr($event->name, 0, 1),
                'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                'last_message_at' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => 0, // TODO: Implement unread count
            ];
        }
        
        // Get user's DM conversations
        $dmUsers = ChatMessage::where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('recipient_id', auth()->id());
            })
            ->where('is_direct_message', true)
            ->with('user', 'recipient')
            ->get()
            ->map(function($msg) {
                return $msg->user_id === auth()->id() ? $msg->recipient : $msg->user;
            })
            ->unique('id');
        
        foreach ($dmUsers as $user) {
            if (!$user) continue;
            
            $lastMessage = ChatMessage::where('is_direct_message', true)
                ->where(function($q) use ($user) {
                    $q->where(function($q2) use ($user) {
                        $q2->where('user_id', auth()->id())
                           ->where('recipient_id', $user->id);
                    })->orWhere(function($q2) use ($user) {
                        $q2->where('user_id', $user->id)
                           ->where('recipient_id', auth()->id());
                    });
                })
                ->orderBy('created_at', 'desc')
                ->first();
            
            $conversations[] = [
                'type' => 'dm',
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => substr($user->name, 0, 1),
                'last_message' => $lastMessage ? $lastMessage->message : 'No messages yet',
                'last_message_at' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => 0, // TODO: Implement unread count
            ];
        }
        
        // Sort by last message time
        usort($conversations, function($a, $b) {
            if (!$a['last_message_at']) return 1;
            if (!$b['last_message_at']) return -1;
            return $b['last_message_at'] <=> $a['last_message_at'];
        });
        
        $this->conversations = $conversations;
    }
    
    public function switchConversation($type, $id)
    {
        $this->activeConversationType = $type;
        $this->activeConversationId = $id;
        
        if ($type === 'event') {
            $this->eventId = $id;
            $this->event = Event::find($id);
            $this->loadOnlineUsers();
        } elseif ($type === 'dm') {
            // Redirect to DM page
            return redirect()->route('chat.dm', ['userId' => $id]);
        }
    }

    public function render()
    {
        $this->loadConversations();
        
        return view('livewire.chat.index', [
            'messages' => $this->messages,
            'pinnedMessages' => $this->pinnedMessages,
        ]);
    }
}
