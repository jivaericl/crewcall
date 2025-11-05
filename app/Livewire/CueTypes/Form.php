<?php

namespace App\Livewire\CueTypes;

use App\Models\CueType;
use App\Models\Event;
use Livewire\Component;

class Form extends Component
{
    public $eventId;
    public $event;
    public $cueTypeId;
    public $cueType;

    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $icon = '';
    public $is_active = true;
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'color' => 'required|string|max:7',
        'icon' => 'nullable|string|max:50',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function mount($eventId, $cueTypeId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        $this->cueTypeId = $cueTypeId;

        if ($cueTypeId) {
            $this->cueType = CueType::findOrFail($cueTypeId);
            
            if ($this->cueType->is_system) {
                abort(403, 'Cannot edit system cue types.');
            }

            $this->name = $this->cueType->name;
            $this->description = $this->cueType->description;
            $this->color = $this->cueType->color;
            $this->icon = $this->cueType->icon;
            $this->is_active = $this->cueType->is_active;
            $this->sort_order = $this->cueType->sort_order;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'event_id' => $this->eventId,
            'is_system' => false,
        ];

        if ($this->cueTypeId) {
            $this->cueType->update($data);
            $message = 'Cue type updated successfully.';
        } else {
            CueType::create($data);
            $message = 'Cue type created successfully.';
        }

        session()->flash('message', $message);
        return redirect()->route('events.cue-types.index', $this->eventId);
    }

    public function render()
    {
        return view('livewire.cue-types.form');
    }
}
