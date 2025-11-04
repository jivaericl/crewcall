<?php

namespace App\Livewire\RunOfShow;

use App\Models\Session;
use App\Models\Segment;
use App\Models\SessionState;
use App\Models\UserRunOfShowPreference;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    public $sessionId;
    public $session;
    public $visibleColumns = [];
    public $showColumnModal = false;
    public $activeSegmentId = null;

    protected $listeners = ['segmentActivated' => 'refreshActiveSegment'];

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->session = Session::with('event')->findOrFail($sessionId);
        
        // Load user preferences
        $this->loadUserPreferences();
        
        // Load active segment
        $this->refreshActiveSegment();
    }

    public function loadUserPreferences()
    {
        $preference = UserRunOfShowPreference::getOrCreate(
            auth()->id(),
            $this->sessionId
        );
        
        $this->visibleColumns = $preference->visible_columns;
    }

    public function refreshActiveSegment()
    {
        $this->activeSegmentId = SessionState::getActiveSegmentId($this->sessionId);
    }

    public function setActiveSegment($segmentId)
    {
        // Set the active segment
        SessionState::setActiveSegment(
            $this->sessionId,
            $segmentId,
            auth()->id()
        );
        
        $this->activeSegmentId = $segmentId;
        
        // Broadcast to all connected clients
        $this->dispatch('segmentActivated', [
            'sessionId' => $this->sessionId,
            'segmentId' => $segmentId,
        ]);
    }

    public function clearActiveSegment()
    {
        SessionState::clearActiveSegment($this->sessionId);
        $this->activeSegmentId = null;
        
        // Broadcast to all connected clients
        $this->dispatch('segmentActivated', [
            'sessionId' => $this->sessionId,
            'segmentId' => null,
        ]);
    }

    public function openColumnModal()
    {
        $this->showColumnModal = true;
    }

    public function closeColumnModal()
    {
        $this->showColumnModal = false;
    }

    public function toggleColumn($column)
    {
        if (in_array($column, $this->visibleColumns)) {
            // Remove column
            $this->visibleColumns = array_values(
                array_filter($this->visibleColumns, fn($col) => $col !== $column)
            );
        } else {
            // Add column
            $this->visibleColumns[] = $column;
        }
    }

    public function saveColumnPreferences()
    {
        // Save preferences to database
        UserRunOfShowPreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'session_id' => $this->sessionId,
            ],
            [
                'visible_columns' => $this->visibleColumns,
            ]
        );
        
        $this->closeColumnModal();
        session()->flash('message', 'Column preferences saved.');
    }

    public function isColumnVisible($column)
    {
        return in_array($column, $this->visibleColumns);
    }

    #[On('segmentActivated')]
    public function handleSegmentActivated($data)
    {
        // Only refresh if it's for this session
        if ($data['sessionId'] == $this->sessionId) {
            $this->refreshActiveSegment();
        }
    }

    public function render()
    {
        $segments = Segment::where('session_id', $this->sessionId)
            ->with(['creator', 'cues'])
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        $availableColumns = UserRunOfShowPreference::availableColumns();

        return view('livewire.run-of-show.index', [
            'segments' => $segments,
            'availableColumns' => $availableColumns,
        ]);
    }
}
