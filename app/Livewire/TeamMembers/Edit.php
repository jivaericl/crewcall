<?php

namespace App\Livewire\TeamMembers;

use App\Models\Event;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public $eventId;
    public $userId;
    public $event;
    public $user;
    
    public $dietary_restrictions;
    public $allergies;
    public $health_notes;
    
    public function mount($eventId, $userId)
    {
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->event = Event::findOrFail($eventId);
        $this->user = User::findOrFail($userId);
        
        // Load current values
        $this->dietary_restrictions = $this->user->dietary_restrictions;
        $this->allergies = $this->user->allergies;
        $this->health_notes = $this->user->health_notes;
    }
    
    public function save()
    {
        $this->user->update([
            'dietary_restrictions' => $this->dietary_restrictions,
            'allergies' => $this->allergies,
            'health_notes' => $this->health_notes,
        ]);
        
        session()->flash('message', 'Health & safety information updated successfully.');
        
        return redirect()->route('events.team-members.show', [$this->eventId, $this->userId]);
    }
    
    public function render()
    {
        return view('livewire.team-members.edit');
    }
}
