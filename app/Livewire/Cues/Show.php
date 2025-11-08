<?php

namespace App\Livewire\Cues;

use App\Models\Cue;
use App\Models\Segment;
use App\Models\AuditLog;
use Livewire\Component;

class Show extends Component
{
    public $segmentId;
    public $cueId;
    public $cue;
    public $segment;
    public $auditLogs;

    public function mount($segmentId, $cueId)
    {
        $this->segmentId = $segmentId;
        $this->cueId = $cueId;
        
        $this->segment = Segment::with('session.event')->findOrFail($segmentId);
        
        $this->cue = Cue::with([
            'segment.session.event',
            'cueType',
            'operator',
            'creator',
            'updater',
            'tags',
            'comments.user',
            'customFieldValues.customField'
        ])->findOrFail($cueId);
        
        // Get audit logs for this cue
        $this->auditLogs = AuditLog::where('auditable_type', Cue::class)
            ->where('auditable_id', $cueId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.cues.show')->layout('layouts.app');
    }
}
