<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Models\Event;
use App\Models\AuditLog;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $speakerId;
    public $event;
    public $speaker;
    public $auditLogs;

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
        
        // Get audit logs for this speaker
        $this->auditLogs = AuditLog::where('auditable_type', Speaker::class)
            ->where('auditable_id', $speakerId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.speakers.show');
    }
}
