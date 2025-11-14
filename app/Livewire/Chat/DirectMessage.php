<?php

namespace App\Livewire\Chat;

use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\On;

class DirectMessage extends Component
{
    public $userId;
    public $recipient;
    public $messages = [];
    public $newMessage = '';
    public $conversations = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->recipient = User::findOrFail($userId);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::directMessages()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                      ->where('recipient_id', $this->userId);
            })
            ->orWhere(function ($query) {
                $query->where('user_id', $this->userId)
                      ->where('recipient_id', auth()->id());
            })
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        $message = ChatMessage::create([
            'user_id' => auth()->id(),
            'recipient_id' => $this->userId,
            'message' => $this->newMessage,
            'is_direct_message' => true,
        ]);

        $this->newMessage = '';
        $this->loadMessages();

        // Dispatch event for real-time updates
        $this->dispatch('message-sent');
    }

    #[On('refresh-dm')]
    public function refreshMessages()
    {
        $this->loadMessages();
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
                'unread_count' => 0,
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
                'unread_count' => 0,
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
        if ($type === 'event') {
            return redirect()->route('chat.index');
        } elseif ($type === 'dm') {
            return redirect()->route('chat.dm', ['userId' => $id]);
        }
    }

    public function render()
    {
        $this->loadConversations();
        
        return view('livewire.chat.direct-message');
    }
}
