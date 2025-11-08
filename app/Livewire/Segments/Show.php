<?php

namespace App\Livewire\Segments;

use App\Models\Segment;
use App\Models\Session;
use App\Models\AuditLog;
use Livewire\Component;

class Show extends Component
{
    public $sessionId;
    public $segmentId;
    public $segment;
    public $session;
    public $auditLogs;
    public $newComment = '';

    public function mount($sessionId, $segmentId)
    {
        $this->sessionId = $sessionId;
        $this->segmentId = $segmentId;
        
        $this->session = Session::with('event')->findOrFail($sessionId);
        
        $this->segment = Segment::with([
            'session.event',
            'producer',
            'client',
            'creator',
            'updater',
            'tags',
            'comments.user',
            'customFieldValues.customField',
            'cues'
        ])->findOrFail($segmentId);
        
        // Get audit logs for this segment
        $this->auditLogs = AuditLog::where('auditable_type', Segment::class)
            ->where('auditable_id', $segmentId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function postComment()
    {
        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        $this->segment->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);

        $this->newComment = '';
        
        // Reload comments
        $this->segment->load('comments.user');
        
        session()->flash('message', 'Comment posted successfully.');
    }

    public function render()
    {
        return view('livewire.segments.show')->layout('layouts.app');
    }
}
