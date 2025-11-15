<?php

namespace App\Livewire\ShowCall;

use Livewire\Component;
use App\Models\Event;
use App\Models\Session;
use App\Models\Segment;
use App\Models\Cue;
use Carbon\Carbon;

class Index extends Component
{
    public $eventId;
    public $event;
    public $selectedSessionId;
    public $selectedSession;
    public $viewMode = 'timeline'; // timeline, table, compact
    public $currentTime;
    public $showCompleted = false;
    public $filterSegmentId = null;
    public $filterCueType = null;
    
    // Cue execution
    public $standbyCueId = null;
    public $executingCueId = null;
    
    // Real-time clock
    public $clockTime;

    protected $listeners = ['refreshShowCall' => '$refresh'];

    public function mount($eventId, $sessionId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        if ($sessionId) {
            $this->selectedSessionId = $sessionId;
            $this->selectedSession = Session::findOrFail($sessionId);
        } else {
            // Select first session by default
            $firstSession = Session::where('event_id', $eventId)
                ->orderBy('start_date')
                ->first();
            
            if ($firstSession) {
                $this->selectedSessionId = $firstSession->id;
                $this->selectedSession = $firstSession;
            }
        }
        
        $this->currentTime = now()->format('H:i:s');
        $this->clockTime = now()->format('g:i:s A');
    }

    public function selectSession($sessionId)
    {
        $this->selectedSessionId = $sessionId;
        $this->selectedSession = Session::findOrFail($sessionId);
        $this->resetFilters();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function toggleShowCompleted()
    {
        $this->showCompleted = !$this->showCompleted;
    }

    public function resetFilters()
    {
        $this->filterSegmentId = null;
        $this->filterCueType = null;
        $this->standbyCueId = null;
        $this->executingCueId = null;
    }

    public function setStandby($cueId)
    {
        $this->standbyCueId = $cueId;
        
        // Update cue status to standby
        $cue = Cue::find($cueId);
        if ($cue) {
            $cue->update(['status' => 'standby']);
        }
    }

    public function executeCue($cueId)
    {
        $this->executingCueId = $cueId;
        
        // Update cue status to complete
        $cue = Cue::find($cueId);
        if ($cue) {
            $cue->update(['status' => 'complete']);
        }
        
        // Auto-advance to next cue
        $this->autoAdvanceToNextCue($cueId);
        
        session()->flash('message', 'Cue executed successfully.');
    }

    public function skipCue($cueId)
    {
        $cue = Cue::find($cueId);
        if ($cue) {
            $cue->update(['status' => 'skip']);
        }
        
        // Auto-advance to next cue
        $this->autoAdvanceToNextCue($cueId);
        
        session()->flash('message', 'Cue skipped.');
    }

    public function resetCue($cueId)
    {
        $cue = Cue::find($cueId);
        if ($cue) {
            $cue->update(['status' => 'standby']);
        }
        
        if ($this->standbyCueId == $cueId) {
            $this->standbyCueId = null;
        }
        if ($this->executingCueId == $cueId) {
            $this->executingCueId = null;
        }
    }

    private function autoAdvanceToNextCue($currentCueId)
    {
        // Find next standby cue
        $currentCue = Cue::find($currentCueId);
        if (!$currentCue) return;
        
        $nextCue = Cue::whereHas('segment', function ($query) {
            $query->whereHas('session', function ($q) {
                $q->where('id', $this->selectedSessionId);
            });
        })
        ->where('time', '>', $currentCue->time)
        ->where('status', 'standby')
        ->orderBy('time')
        ->first();
        
        if ($nextCue) {
            $this->setStandby($nextCue->id);
        } else {
            $this->standbyCueId = null;
        }
    }

    public function updateClock()
    {
        $this->clockTime = now()->format('g:i:s A');
        $this->currentTime = now()->format('H:i:s');
    }

    // Shorthand methods for button actions
    public function standby($cueId)
    {
        $this->setStandby($cueId);
    }

    public function go($cueId)
    {
        $this->executeCue($cueId);
    }

    public function skip($cueId)
    {
        $this->skipCue($cueId);
    }

    public function resetCueStatus($cueId)
    {
        $this->resetCue($cueId);
    }

    public function render()
    {
        // Get all sessions for this event
        $sessions = Session::where('event_id', $this->eventId)
            ->orderBy('start_date')
            ->get();
        
        $segments = collect();
        $cues = collect();
        $cueTypes = collect();
        
        if ($this->selectedSession) {
            // Get segments for selected session
            $segmentsQuery = Segment::where('session_id', $this->selectedSessionId)
                ->with(['client', 'producer', 'tags'])
                ->orderBy('start_time');
            
            if ($this->filterSegmentId) {
                $segmentsQuery->where('id', $this->filterSegmentId);
            }
            
            $segments = $segmentsQuery->get();
            
            // Get cues for selected session
            $cuesQuery = Cue::whereHas('segment', function ($query) {
                $query->where('session_id', $this->selectedSessionId);
            })
            ->with(['segment', 'cueType', 'operator', 'tags']);
            
            if (!$this->showCompleted) {
                $cuesQuery->where('status', 'standby');
            }
            
            if ($this->filterSegmentId) {
                $cuesQuery->where('segment_id', $this->filterSegmentId);
            }
            
            if ($this->filterCueType) {
                $cuesQuery->where('cue_type_id', $this->filterCueType);
            }
            
            $cues = $cuesQuery->orderBy('time')->get();
            
            // Get available cue types
            $cueTypes = \App\Models\CueType::active()
                ->where(function ($query) {
                    $query->where('event_id', $this->eventId)
                          ->orWhereNull('event_id');
                })
                ->ordered()
                ->get();
        }
        
        return view('livewire.show-call.index', [
            'sessions' => $sessions,
            'segments' => $segments,
            'cues' => $cues,
            'cueTypes' => $cueTypes,
        ]);
    }
}
