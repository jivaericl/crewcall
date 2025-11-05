<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Models\Segment;
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
                  ->orWhere('description', 'like', '%' . $this->search . '%');
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
}
