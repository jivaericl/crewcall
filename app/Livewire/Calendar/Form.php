<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarItem;
use App\Models\Event;
use App\Models\User;
use App\Models\Speaker;
use App\Models\Tag;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    public $eventId;
    public $event;
    public $calendarItemId;
    public $calendarItem;

    // Form fields
    public $type = 'milestone';
    public $title;
    public $description;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $all_day = false;
    public $location;
    public $color;

    // Relationships
    public $selectedUsers = [];
    public $selectedSpeakers = [];
    public $selectedTags = [];

    // Available options
    public $availableUsers = [];
    public $availableSpeakers = [];
    public $availableTags = [];

    protected function rules()
    {
        return [
            'type' => 'required|in:milestone,out_of_office,call',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'selectedUsers' => 'array',
            'selectedSpeakers' => 'array',
            'selectedTags' => 'array',
        ];
    }

    public function mount($eventId, $calendarItemId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);

        // Load available options
        $this->loadAvailableOptions();

        if ($calendarItemId) {
            // Editing existing calendar item
            $this->calendarItemId = $calendarItemId;
            $this->calendarItem = CalendarItem::with(['users', 'speakers', 'tags'])
                ->findOrFail($calendarItemId);
            
            // Check if calendar item belongs to this event
            if ($this->calendarItem->event_id != $this->eventId) {
                abort(403);
            }

            $this->loadCalendarItem();
        } else {
            // Creating new calendar item - set default color based on type
            $this->setDefaultColor();
        }
    }

    protected function loadAvailableOptions()
    {
        // Get users assigned to this event
        $this->availableUsers = User::whereHas('eventAssignments', function($query) {
            $query->where('event_id', $this->eventId);
        })->orderBy('name')->get();

        // Get speakers for this event
        $this->availableSpeakers = Speaker::where('event_id', $this->eventId)
            ->orderBy('name')
            ->get();

        // Get tags for this event with calendar-related types
        $this->availableTags = Tag::where('event_id', $this->eventId)
            ->whereIn('model_type', ['event', 'milestone', 'out_of_office', 'call'])
            ->orderBy('name')
            ->get();
    }

    protected function loadCalendarItem()
    {
        $this->type = $this->calendarItem->type;
        $this->title = $this->calendarItem->title;
        $this->description = $this->calendarItem->description;
        
        // Split datetime into date and time
        $startDateTime = \Carbon\Carbon::parse($this->calendarItem->start_date);
        $this->start_date = $startDateTime->format('Y-m-d');
        $this->start_time = $startDateTime->format('H:i');
        
        $endDateTime = \Carbon\Carbon::parse($this->calendarItem->end_date);
        $this->end_date = $endDateTime->format('Y-m-d');
        $this->end_time = $endDateTime->format('H:i');
        
        $this->all_day = $this->calendarItem->all_day;
        $this->location = $this->calendarItem->location;
        $this->color = $this->calendarItem->color;

        // Load relationships
        $this->selectedUsers = $this->calendarItem->users->pluck('id')->toArray();
        $this->selectedSpeakers = $this->calendarItem->speakers->pluck('id')->toArray();
        $this->selectedTags = $this->calendarItem->tags->pluck('id')->toArray();
    }

    public function updatedType()
    {
        // Set default color when type changes (only if creating new)
        if (!$this->calendarItemId) {
            $this->setDefaultColor();
        }
    }

    protected function setDefaultColor()
    {
        $this->color = match($this->type) {
            'milestone' => '#10B981',      // Green
            'out_of_office' => '#F59E0B',  // Amber
            'call' => '#3B82F6',           // Blue
            default => '#6B7280',          // Gray
        };
    }

    public function save()
    {
        $this->validate();

        // Combine date and time
        $start_datetime = $this->start_date . ' ' . ($this->start_time ?: '00:00:00');
        $end_datetime = $this->end_date . ' ' . ($this->end_time ?: '23:59:59');

        $data = [
            'event_id' => $this->eventId,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $start_datetime,
            'end_date' => $end_datetime,
            'all_day' => $this->all_day,
            'location' => $this->location,
            'color' => $this->color,
            'is_active' => true,
        ];

        if ($this->calendarItemId) {
            // Update existing
            $data['updated_by'] = Auth::id();
            $this->calendarItem->update($data);
            $calendarItem = $this->calendarItem;
            $message = 'Calendar item updated successfully.';
        } else {
            // Create new
            $data['created_by'] = Auth::id();
            $calendarItem = CalendarItem::create($data);
            $message = 'Calendar item created successfully.';
        }

        // Sync relationships
        $calendarItem->users()->sync($this->selectedUsers);
        $calendarItem->speakers()->sync($this->selectedSpeakers);
        $calendarItem->tags()->sync($this->selectedTags);

        session()->flash('message', $message);

        return redirect()->route('events.calendar.show', [$this->eventId, $calendarItem->id]);
    }

    public function render()
    {
        return view('livewire.calendar.form');
    }
}
