<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Models\Event;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $speakerId;
    public $event;
    public $speaker;

    public function mount($eventId, $speakerId)
    {
        $this->eventId = $eventId;
        $this->speakerId = $speakerId;
        $this->event = Event::findOrFail($eventId);
        $this->speaker = Speaker::with([
            'sessions.event',
            'contentFiles',
            'tags',
            'user',
            'creator',
            'updater',
            'comments.user'
        ])->findOrFail($speakerId);
    }

    public function render()
    {
        return view('livewire.speakers.show');
    }
}
