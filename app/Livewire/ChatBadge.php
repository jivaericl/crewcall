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
        // Messages from other users in events the user is part of
        $this->unreadCount = ChatMessage::whereHas('event.users', function ($query) {
            $query->where('users.id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->where('created_at', '>', now()->subDays(7)) // Only count recent messages
        ->whereNull('read_at')
        ->count();
    }

    public function render()
    {
        return view('livewire.chat-badge');
    }
}
