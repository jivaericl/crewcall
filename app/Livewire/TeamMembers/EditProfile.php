<?php

namespace App\Livewire\TeamMembers;

use App\Models\Event;
use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class EditProfile extends Component
{
    public $eventId;
    public $userId;
    public $event;
    public $user;
    
    // User fields
    public $name;
    public $email;
    public $first_name;
    public $last_name;
    public $timezone;
    
    // Health & Safety
    public $dietary_restrictions;
    public $allergies;
    public $health_notes;
    
    // Emergency Contact
    public $emergency_contact_first_name;
    public $emergency_contact_last_name;
    public $emergency_contact_relationship;
    public $emergency_contact_phone;
    public $emergency_contact_email;
    
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'timezone' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'health_notes' => 'nullable|string',
            'emergency_contact_first_name' => 'nullable|string|max:255',
            'emergency_contact_last_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_email' => 'nullable|email|max:255',
        ];
    }
    
    public function mount($eventId, $userId)
    {
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->event = Event::findOrFail($eventId);
        $this->user = User::findOrFail($userId);
        
        // Load current values
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->timezone = $this->user->timezone ?? 'UTC';
        $this->dietary_restrictions = $this->user->dietary_restrictions;
        $this->allergies = $this->user->allergies;
        $this->health_notes = $this->user->health_notes;
        $this->emergency_contact_first_name = $this->user->emergency_contact_first_name;
        $this->emergency_contact_last_name = $this->user->emergency_contact_last_name;
        $this->emergency_contact_relationship = $this->user->emergency_contact_relationship;
        $this->emergency_contact_phone = $this->user->emergency_contact_phone;
        $this->emergency_contact_email = $this->user->emergency_contact_email;
    }
    
    public function save()
    {
        $this->validate();
        
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'timezone' => $this->timezone,
            'dietary_restrictions' => $this->dietary_restrictions,
            'allergies' => $this->allergies,
            'health_notes' => $this->health_notes,
            'emergency_contact_first_name' => $this->emergency_contact_first_name,
            'emergency_contact_last_name' => $this->emergency_contact_last_name,
            'emergency_contact_relationship' => $this->emergency_contact_relationship,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'emergency_contact_email' => $this->emergency_contact_email,
        ]);
        
        session()->flash('message', 'User profile updated successfully.');
        
        return redirect()->route('events.team-members.show', [$this->eventId, $this->userId]);
    }
    
    public function render()
    {
        return view('livewire.team-members.edit-profile');
    }
}
