<?php

namespace App\Livewire\Segments;

use App\Models\Segment;
use App\Models\Session;
use App\Models\User;
use App\Models\Tag;
use Livewire\Component;
use Carbon\Carbon;

class Form extends Component
{
    public $sessionId;
    public $session;
    public $segmentId;
    public $name = '';
    public $code = '';
    public $start_time = '';
    public $end_time = '';
    public $client_id = '';
    public $producer_id = '';
    public $selectedTags = [];
    public $duration = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'code' => 'nullable|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'client_id' => 'nullable|exists:users,id',
            'producer_id' => 'nullable|exists:users,id',
        ];
    }

    protected $messages = [
        'name.required' => 'Segment name is required.',
        'name.min' => 'Segment name must be at least 3 characters.',
        'end_time.after' => 'The end time must be after the start time.',
    ];

    public function mount($sessionId, $segmentId = null)
    {
        $this->sessionId = $sessionId;
        $this->session = Session::with('event')->findOrFail($sessionId);
        
        if ($segmentId) {
            $this->segmentId = $segmentId;
            $segment = Segment::with('tags')->where('session_id', $sessionId)->findOrFail($segmentId);
            
            $this->name = $segment->name;
            $this->code = $segment->code;
            $this->start_time = Carbon::parse($segment->start_time)->format('H:i');
            $this->end_time = Carbon::parse($segment->end_time)->format('H:i');
            $this->client_id = $segment->client_id;
            $this->producer_id = $segment->producer_id;
            
            // Load tags
            $this->selectedTags = $segment->tags->pluck('id')->toArray();
            
            $this->calculateDuration();
        } else {
            // Set default times
            $this->start_time = '09:00';
            $this->end_time = '10:00';
        }
    }

    public function updated($propertyName)
    {
        // Real-time validation
        $this->validateOnly($propertyName);
        
        // Recalculate duration when times change
        if (in_array($propertyName, ['start_time', 'end_time'])) {
            $this->calculateDuration();
        }
    }

    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            try {
                $start = Carbon::createFromFormat('H:i', $this->start_time);
                $end = Carbon::createFromFormat('H:i', $this->end_time);
                
                if ($end->greaterThan($start)) {
                    $diff = $start->diff($end);
                    
                    $parts = [];
                    if ($diff->h > 0) {
                        $parts[] = $diff->h . ' ' . ($diff->h === 1 ? 'hour' : 'hours');
                    }
                    if ($diff->i > 0) {
                        $parts[] = $diff->i . ' ' . ($diff->i === 1 ? 'minute' : 'minutes');
                    }
                    
                    $this->duration = !empty($parts) ? implode(', ', $parts) : '0 minutes';
                } else {
                    $this->duration = '';
                }
            } catch (\Exception $e) {
                $this->duration = '';
            }
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->segmentId) {
            // Update existing segment
            $segment = Segment::where('session_id', $this->sessionId)->findOrFail($this->segmentId);
            $segment->update([
                'name' => $this->name,
                'code' => $this->code,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'client_id' => $this->client_id ?: null,
                'producer_id' => $this->producer_id ?: null,
            ]);
            
            $message = "Segment \"{$segment->name}\" updated successfully.";
        } else {
            // Create new segment
            $segment = Segment::create([
                'session_id' => $this->sessionId,
                'name' => $this->name,
                'code' => $this->code,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'client_id' => $this->client_id ?: null,
                'producer_id' => $this->producer_id ?: null,
            ]);
            
            $message = "Segment \"{$segment->name}\" created successfully.";
        }
        
        // Sync tags
        $segment->tags()->sync($this->selectedTags);

        session()->flash('message', $message);
        
        return redirect()->route('sessions.segments.index', $this->sessionId);
    }

    public function render()
    {
        // Get client users (users with 'Client' role)
        $clientRole = \App\Models\Role::where('slug', 'client')->first();
        $clients = User::whereHas('assignedEvents', function ($query) use ($clientRole) {
            $query->where('event_id', $this->session->event_id)
                  ->where('role_id', $clientRole?->id);
        })->orderBy('name')->get();

        // Get all users assigned to this event as potential producers
        $producers = User::whereHas('assignedEvents', function ($query) {
            $query->where('event_id', $this->session->event_id);
        })->orderBy('name')->get();
        
        // Get all tags
        $allTags = Tag::orderBy('name')->get();

        return view('livewire.segments.form', [
            'clients' => $clients,
            'producers' => $producers,
            'allTags' => $allTags,
        ]);
    }
}
