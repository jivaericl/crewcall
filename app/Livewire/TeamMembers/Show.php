<?php

namespace App\Livewire\TeamMembers;

use App\Models\Event;
use App\Models\User;
use App\Models\Travel;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $userId;
    public $event;
    public $user;
    public $travel;
    public $assignments = [];

    public function mount($eventId, $userId)
    {
        $this->eventId = $eventId;
        $this->userId = $userId;
        
        $this->event = Event::findOrFail($eventId);
        $this->user = User::findOrFail($userId);
        
        // Get travel information
        $this->travel = Travel::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->with(['flights', 'hotelReservations.hotel'])
            ->first();
        
        // Get assignments (roles in this event)
        $this->loadAssignments();
    }

    public function loadAssignments()
    {
        // Get user's role assignments for this event
        $eventUser = $this->event->users()->where('user_id', $this->userId)->first();
        
        if ($eventUser) {
            $this->assignments[] = [
                'type' => 'Event Role',
                'name' => $eventUser->pivot->role_name ?? 'Team Member',
                'details' => 'Assigned to event'
            ];
        }
        
        // Get speaker assignments
        $speakers = $this->event->speakers()->where('user_id', $this->userId)->get();
        foreach ($speakers as $speaker) {
            $this->assignments[] = [
                'type' => 'Speaker',
                'name' => $speaker->name,
                'details' => $speaker->bio ? substr($speaker->bio, 0, 100) . '...' : ''
            ];
        }
        
        // Get cue operator assignments
        $cues = $this->event->cues()->where('operator_id', $this->userId)->get();
        if ($cues->count() > 0) {
            $this->assignments[] = [
                'type' => 'Cue Operator',
                'name' => $cues->count() . ' cue(s) assigned',
                'details' => 'Responsible for executing cues'
            ];
        }
    }

    public function render()
    {
        return view('livewire.team-members.show');
    }
}
