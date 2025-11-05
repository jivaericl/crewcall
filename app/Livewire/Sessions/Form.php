<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Models\Event;
use App\Models\User;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\Tag;
use App\Models\Speaker;
use Livewire\Component;
use Carbon\Carbon;

class Form extends Component
{
    public $eventId;
    public $event;
    public $sessionId;
    public $name = '';
    public $code = '';
    public $description = '';
    public $location = '';
    public $start_date = '';
    public $end_date = '';
    public $client_id = '';
    public $producer_id = '';
    public $customFieldValues = [];
    public $selectedTags = [];
    public $selectedSpeakers = [];
    public $duration = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'client_id' => 'nullable|exists:contacts,id',
            'producer_id' => 'nullable|exists:contacts,id',
        ];
    }

    protected $messages = [
        'name.required' => 'Session name is required.',
        'name.min' => 'Session name must be at least 3 characters.',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
    ];

    public function mount($eventId, $sessionId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        if ($sessionId) {
            $this->sessionId = $sessionId;
            $session = Session::with(['customFieldValues', 'tags'])->where('event_id', $eventId)->findOrFail($sessionId);
            
            $this->name = $session->name;
            $this->code = $session->code;
            $this->description = $session->description;
            $this->location = $session->location;
            $this->start_date = $session->start_date->format('Y-m-d\TH:i');
            $this->end_date = $session->end_date->format('Y-m-d\TH:i');
            $this->client_id = $session->client_id;
            $this->producer_id = $session->producer_id;
            
            // Load custom field values
            foreach ($session->customFieldValues as $value) {
                $this->customFieldValues[$value->custom_field_id] = $value->value;
            }
            
            // Load tags
            $this->selectedTags = $session->tags->pluck('id')->toArray();
            
            // Load speakers
            $this->selectedSpeakers = $session->speakers->pluck('id')->toArray();
            
            $this->calculateDuration();
        } else {
            // Set default dates from event
            $this->start_date = $this->event->start_date->format('Y-m-d\TH:i');
            $this->end_date = $this->event->start_date->copy()->addHour()->format('Y-m-d\TH:i');
        }
    }

    public function updated($propertyName)
    {
        // Real-time validation
        if (!str_starts_with($propertyName, 'customFieldValues.')) {
            $this->validateOnly($propertyName);
        }
        
        // Recalculate duration when dates change
        if (in_array($propertyName, ['start_date', 'end_date'])) {
            $this->calculateDuration();
        }
    }

    public function calculateDuration()
    {
        if ($this->start_date && $this->end_date) {
            try {
                $start = Carbon::parse($this->start_date);
                $end = Carbon::parse($this->end_date);
                
                if ($end->greaterThanOrEqualTo($start)) {
                    $diff = $start->diff($end);
                    
                    $parts = [];
                    if ($diff->d > 0) $parts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                    if ($diff->h > 0) $parts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                    if ($diff->i > 0) $parts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                    
                    $this->duration = !empty($parts) ? implode(', ', $parts) : 'Less than a minute';
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

        // Validate custom fields
        $customFields = CustomField::forEvent($this->eventId)->forModelType('session')->get();
        foreach ($customFields as $field) {
            if ($field->is_required && empty($this->customFieldValues[$field->id])) {
                $this->addError('customFieldValues.' . $field->id, $field->name . ' is required.');
                return;
            }
        }

        if ($this->sessionId) {
            // Update existing session
            $session = Session::where('event_id', $this->eventId)->findOrFail($this->sessionId);
            $session->update([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'location' => $this->location,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'client_id' => $this->client_id ?: null,
                'producer_id' => $this->producer_id ?: null,
            ]);
            
            $message = "Session \"{$session->name}\" updated successfully.";
        } else {
            // Create new session
            $session = Session::create([
                'event_id' => $this->eventId,
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
                'location' => $this->location,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'client_id' => $this->client_id ?: null,
                'producer_id' => $this->producer_id ?: null,
            ]);
            
            $message = "Session \"{$session->name}\" created successfully.";
        }

        // Save custom field values
        foreach ($this->customFieldValues as $fieldId => $value) {
            if ($value !== null && $value !== '') {
                $session->setCustomFieldValue($fieldId, $value);
            }
        }
        
        // Sync tags
        $session->tags()->sync($this->selectedTags);
        
        // Sync speakers
        $session->speakers()->sync($this->selectedSpeakers);

        session()->flash('message', $message);
        
        return redirect()->route('events.sessions.index', $this->eventId);
    }

    public function render()
    {
        // Get contacts for client/producer selection
        $clients = Contact::where('event_id', $this->eventId)
            ->where('contact_type', 'client')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $producers = Contact::where('event_id', $this->eventId)
            ->whereIn('contact_type', ['producer', 'staff'])
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get custom fields for sessions in this event
        $customFields = CustomField::forEvent($this->eventId)->forModelType('session')->ordered()->get();
        
        // Get all tags
        $allTags = Tag::orderBy('name')->get();
        
        // Get all speakers for this event
        $allSpeakers = Speaker::where('event_id', $this->eventId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.sessions.form', [
            'clients' => $clients,
            'producers' => $producers,
            'customFields' => $customFields,
            'allTags' => $allTags,
            'allSpeakers' => $allSpeakers,
        ]);
    }
}
