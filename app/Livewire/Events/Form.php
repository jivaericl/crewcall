<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Tag;
use App\Models\CustomField;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Form extends Component
{
    public $eventId;
    public $name = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $timezone = 'UTC';
    public $selectedTags = [];
    public $newTagName = '';
    public $newTagColor = '#3b82f6';
    public $showTagModal = false;
    public $duration = '';
    public $customFields = [];

    protected $rules = [
        'name' => 'required|string|max:255|min:3',
        'description' => 'nullable|string|max:5000',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'timezone' => 'required|string',
        'selectedTags' => 'array|max:10',
    ];

    protected $messages = [
        'name.required' => 'Event name is required.',
        'name.min' => 'Event name must be at least 3 characters.',
        'name.max' => 'Event name cannot exceed 255 characters.',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        'selectedTags.max' => 'You can select a maximum of 10 tags.',
    ];

    public function mount($eventId = null)
    {
        // Detect user's timezone from browser (will be set via JavaScript)
        $this->timezone = 'UTC';
        
        if ($eventId) {
            $this->eventId = $eventId;
            $event = Event::with('tags')->findOrFail($eventId);
            
            $this->name = $event->name;
            $this->description = $event->description;
            $this->start_date = $event->start_date->format('Y-m-d\TH:i');
            $this->end_date = $event->end_date->format('Y-m-d\TH:i');
            $this->timezone = $event->timezone;
            $this->selectedTags = $event->tags->pluck('id')->toArray();
            
            // Load custom field values
            foreach ($event->customFieldValues as $value) {
                $this->customFields[$value->custom_field_id] = $value->value;
            }
            
            $this->calculateDuration();
        }
    }

    public function updated($propertyName)
    {
        // Real-time validation
        $this->validateOnly($propertyName);
        
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

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function openTagModal()
    {
        $this->showTagModal = true;
        $this->newTagName = '';
        $this->newTagColor = '#3b82f6';
    }

    public function closeTagModal()
    {
        $this->showTagModal = false;
        $this->newTagName = '';
        $this->newTagColor = '#3b82f6';
    }

    public function createTag()
    {
        $this->validate([
            'newTagName' => 'required|string|max:255|unique:tags,name',
            'newTagColor' => 'required|string',
        ], [
            'newTagName.required' => 'Tag name is required.',
            'newTagName.unique' => 'A tag with this name already exists.',
        ]);

        $tag = Tag::create([
            'name' => $this->newTagName,
            'color' => $this->newTagColor,
        ]);

        $this->selectedTags[] = $tag->id;
        
        session()->flash('tag-message', 'Tag "' . $tag->name . '" created successfully.');
        
        $this->closeTagModal();
    }

    public function save()
    {
        $this->validate();

        if ($this->eventId) {
            // Update existing event
            $event = Event::findOrFail($this->eventId);
            $event->update([
                'name' => $this->name,
                'description' => $this->description,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'timezone' => $this->timezone,
            ]);
            
            $message = 'Event "' . $event->name . '" updated successfully.';
        } else {
            // Create new event
            $event = Event::create([
                'name' => $this->name,
                'description' => $this->description,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'timezone' => $this->timezone,
            ]);
            
            // Automatically assign creator as admin (if roles exist)
            $adminRole = \App\Models\Role::where('slug', 'admin')->orWhere('id', 1)->first();
            if ($adminRole) {
                $event->assignedUsers()->attach(auth()->id(), [
                    'role_id' => $adminRole->id,
                    'is_admin' => true,
                ]);
            }
            
            $message = 'Event "' . $event->name . '" created successfully.';
        }

        // Sync tags
        $event->tags()->sync($this->selectedTags);
        
        // Sync custom fields
        $event->syncCustomFields($this->customFields);

        session()->flash('message', $message);
        
        return redirect()->route('events.index');
    }

    public function render()
    {
        $tags = Tag::orderBy('name')->get();
        $timezones = \DateTimeZone::listIdentifiers();
        
        // Get custom fields for events
        $customFieldsList = collect();
        if ($this->eventId) {
            $customFieldsList = CustomField::forEvent($this->eventId)
                ->forModelType('event')
                ->ordered()
                ->get();
        }

        return view('livewire.events.form', [
            'tags' => $tags,
            'timezones' => $timezones,
            'customFieldsList' => $customFieldsList,
        ]);
    }
}
