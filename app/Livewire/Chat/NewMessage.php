<?php

namespace App\Livewire\Chat;

use App\Models\User;
use App\Models\ChatMessage;
use Livewire\Component;
use Livewire\Attributes\On;

class NewMessage extends Component
{
    public $isOpen = false;
    public $recipients = [];
    public $message = '';
    public $searchQuery = '';
    public $searchResults = [];

    #[On('openNewMessage')]
    public function open()
    {
        $this->isOpen = true;
        $this->reset(['recipients', 'message', 'searchQuery', 'searchResults']);
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['recipients', 'message', 'searchQuery', 'searchResults']);
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = User::where('id', '!=', auth()->id())
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
            })
            ->whereNotIn('id', array_column($this->recipients, 'id'))
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function addRecipient($userId)
    {
        $user = User::find($userId);
        if ($user && !in_array($userId, array_column($this->recipients, 'id'))) {
            $this->recipients[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }
        $this->searchQuery = '';
        $this->searchResults = [];
    }

    public function removeRecipient($userId)
    {
        $this->recipients = array_filter($this->recipients, function($recipient) use ($userId) {
            return $recipient['id'] != $userId;
        });
        $this->recipients = array_values($this->recipients); // Re-index array
    }

    public function sendMessage()
    {
        $this->validate([
            'recipients' => 'required|array|min:1',
            'message' => 'required|string|max:2000',
        ]);

        foreach ($this->recipients as $recipient) {
            ChatMessage::create([
                'user_id' => auth()->id(),
                'recipient_id' => $recipient['id'],
                'message' => $this->message,
                'is_direct_message' => true,
            ]);
        }

        session()->flash('message', 'Message sent to ' . count($this->recipients) . ' ' . str('person')->plural(count($this->recipients)));
        
        $this->close();
        $this->dispatch('message-sent');
        
        // If only one recipient, redirect to that DM
        if (count($this->recipients) === 1) {
            return redirect()->route('chat.dm', ['userId' => $this->recipients[0]['id']]);
        }
    }

    public function render()
    {
        return view('livewire.chat.new-message');
    }
}
