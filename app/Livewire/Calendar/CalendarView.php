<?php

namespace App\Livewire\Calendar;

use App\Models\Event;
use Livewire\Component;

class CalendarView extends Component
{
    public $eventId;
    public $event;
    
    // Filter toggles
    public $showMilestones = true;
    public $showOutOfOffice = true;
    public $showCalls = true;
    public $showSessions = true;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);
    }

    public function toggleMilestones()
    {
        $this->showMilestones = !$this->showMilestones;
        $this->dispatchFilterChanged();
    }

    public function toggleOutOfOffice()
    {
        $this->showOutOfOffice = !$this->showOutOfOffice;
        $this->dispatchFilterChanged();
    }

    public function toggleCalls()
    {
        $this->showCalls = !$this->showCalls;
        $this->dispatchFilterChanged();
    }

    public function toggleSessions()
    {
        $this->showSessions = !$this->showSessions;
        $this->dispatchFilterChanged();
    }
    
    private function dispatchFilterChanged()
    {
        $this->dispatch('filterChanged', [
            'showMilestones' => $this->showMilestones,
            'showOutOfOffice' => $this->showOutOfOffice,
            'showCalls' => $this->showCalls,
            'showSessions' => $this->showSessions,
        ]);
    }

    public function render()
    {
        return view('livewire.calendar.calendar-view');
    }
}
