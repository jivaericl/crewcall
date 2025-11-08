<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\AuditLog;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $event;
    public $auditLogs;
    
    // Comments
    public $newComment = '';

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::with(['creator', 'updater', 'tags', 'comments.user', 'sessions', 'speakers', 'contacts'])
            ->findOrFail($eventId);
        
        // Get audit logs for this event
        $this->auditLogs = AuditLog::where('auditable_type', Event::class)
            ->where('auditable_id', $eventId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.events.show');
    }
    
    public function postComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        $this->event->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);

        $this->newComment = '';
        
        // Reload comments
        $this->event->load('comments.user');
        
        session()->flash('message', 'Comment posted successfully.');
    }
}
