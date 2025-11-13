<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatBadge extends Component
{
    public $unreadCount = 0;

    protected $listeners = ['messageReceived' => 'updateUnreadCount'];

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount()
    {
        // Get count of unread messages for the current user
        $this->unreadCount = ChatMessage::whereHas('room.participants', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->where('created_at', '>', function ($query) {
            $query->selectRaw('COALESCE(MAX(read_at), ?)', [now()->subDays(30)])
                ->from('chat_participants')
                ->whereColumn('chat_participants.chat_room_id', 'chat_messages.chat_room_id')
                ->where('chat_participants.user_id', Auth::id());
        })
        ->count();
    }

    public function render()
    {
        return view('livewire.chat-badge');
    }
}
