<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class Settings extends Component
{
    public $chat_sound_enabled;
    public $chat_widget_enabled;
    public $chat_notifications_enabled;
    public $chat_desktop_notifications;

    public function mount()
    {
        $user = auth()->user();
        $this->chat_sound_enabled = $user->chat_sound_enabled;
        $this->chat_widget_enabled = $user->chat_widget_enabled;
        $this->chat_notifications_enabled = $user->chat_notifications_enabled;
        $this->chat_desktop_notifications = $user->chat_desktop_notifications;
    }

    public function save()
    {
        auth()->user()->update([
            'chat_sound_enabled' => $this->chat_sound_enabled,
            'chat_widget_enabled' => $this->chat_widget_enabled,
            'chat_notifications_enabled' => $this->chat_notifications_enabled,
            'chat_desktop_notifications' => $this->chat_desktop_notifications,
        ]);

        session()->flash('message', 'Chat settings saved successfully!');
        
        // Dispatch event to refresh widget
        $this->dispatch('chat-settings-updated');
    }

    public function render()
    {
        return view('livewire.chat.settings');
    }
}
