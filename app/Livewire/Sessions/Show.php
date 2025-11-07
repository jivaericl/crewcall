<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Models\Segment;
use App\Models\Cue;
use App\Models\CueType;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $sessionId;
    public $session;
    
    // Filters
    public $filterCueType = '';
    public $search = '';
    
    // Reset confirmation
    public $showResetModal = false;
    public $resetConfirmation = '';

    public function mount($eventId, $sessionId)
    {
        $this->eventId = $eventId;
        $this->sessionId = $sessionId;
        $this->session = Session::with(['event', 'client', 'producer'])
            ->findOrFail($sessionId);
    }

    public function render()
    {
        // Get segments with cues, ordered by sort_order and start_time
        $segmentsQuery = Segment::where('session_id', $this->sessionId)
            ->with(['cues' => function($query) {
                $query->with(['cueType', 'operator'])
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('time', 'asc');
                
                // Apply cue type filter
                if ($this->filterCueType) {
                    $query->where('cue_type_id', $this->filterCueType);
                }
                
                // Apply search
                if ($this->search) {
                    $query->where(function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('notes', 'like', '%' . $this->search . '%');
                    });
                }
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('start_time', 'asc');

        // Apply search to segments
        if ($this->search) {
            $segmentsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $segments = $segmentsQuery->get();

        // Get cue types for filter
        $cueTypes = CueType::active()->forEvent($this->eventId)->ordered()->get();

        return view('livewire.sessions.show', [
            'segments' => $segments,
            'cueTypes' => $cueTypes,
        ]);
    }
    
    public function activateCue($cueId)
    {
        // Find the cue being activated
        $cue = Cue::findOrFail($cueId);
        
        // If this cue is already GO, do nothing
        if ($cue->status === 'go') {
            return;
        }
        
        // Find any cue that is currently GO in this session and mark it as complete
        Cue::whereHas('segment', function($query) {
                $query->where('session_id', $this->sessionId);
            })
            ->where('status', 'go')
            ->update(['status' => 'complete']);
        
        // Set this cue to GO
        $cue->status = 'go';
        $cue->save();
        
        session()->flash('message', 'Cue activated.');
    }
    
    public function openResetModal()
    {
        $this->showResetModal = true;
        $this->resetConfirmation = '';
    }
    
    public function closeResetModal()
    {
        $this->showResetModal = false;
        $this->resetConfirmation = '';
    }
    
    public function resetAllCues()
    {
        // Validate confirmation
        if ($this->resetConfirmation !== 'RESET') {
            session()->flash('error', 'You must type RESET to confirm.');
            return;
        }
        
        // Reset all cues in this session to standby
        Cue::whereHas('segment', function($query) {
                $query->where('session_id', $this->sessionId);
            })
            ->update(['status' => 'standby']);
        
        $this->closeResetModal();
        session()->flash('message', 'All cues have been reset to standby.');
    }
}
