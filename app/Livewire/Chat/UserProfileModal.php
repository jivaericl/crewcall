<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class UserProfileModal extends Component
{
    public $isOpen = false;
    public $userId;
    public $user;
    
    #[On('showUserProfile')]
    public function showUserProfile($userId)
    {
        $this->userId = $userId;
        $this->user = User::find($userId);
        $this->isOpen = true;
    }
    
    public function close()
    {
        $this->isOpen = false;
        $this->user = null;
    }
    
    public function startDirectMessage()
    {
        if ($this->userId) {
            return redirect()->route('chat.dm', ['userId' => $this->userId]);
        }
    }

    public function render()
    {
        return view('livewire.chat.user-profile-modal');
    }
}
