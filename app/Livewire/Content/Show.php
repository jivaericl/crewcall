<?php

namespace App\Livewire\Content;

use App\Models\ContentFile;
use App\Models\Event;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $contentId;
    public $event;
    public $content;

    public function mount($eventId, $contentId)
    {
        $this->eventId = $eventId;
        $this->contentId = $contentId;
        $this->event = Event::findOrFail($eventId);
        
        $this->content = ContentFile::with([
            'category',
            'creator',
            'updater',
            'tags',
            'speakers',
            'segments.session',
            'cues.segment.session',
            'versions.uploader',
            'comments.user'
        ])->findOrFail($contentId);

        // Ensure content belongs to this event
        if ($this->content->event_id != $this->eventId) {
            abort(404);
        }

        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);
    }

    public function render()
    {
        return view('livewire.content.show')->layout('layouts.app');
    }
}
