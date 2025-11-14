<?php

namespace App\Livewire\Chat;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class DirectMessage extends Component
{
    public $userId;
    public $recipient;
    public $messages = [];
    public $newMessage = '';

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

    public function render()
    {
        return view('livewire.chat.direct-message');
    }
}
