<?php

namespace App\Livewire\Cues;

use Livewire\Component;
use App\Models\Cue;
use App\Models\Segment;
use App\Models\CueType;
use App\Models\User;
use App\Models\Tag;

class Form extends Component
{
    public $segmentId;
    public $segment;
    public $cueId;
    public $cue;
    
    public $name;
    public $code;
    public $cue_type_id;
    public $description;
    public $time;
    public $status = 'standby';
    public $notes;
    public $filename;
    public $operator_id;
    public $priority = 'normal';
    public $selectedTags = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'code' => 'nullable|string|max:50',
            'cue_type_id' => 'required|exists:cue_types,id',
            'description' => 'nullable|string',
            'time' => 'nullable|date_format:H:i',
            'status' => 'required|in:standby,go,complete,skip',
            'notes' => 'nullable|string',
            'filename' => 'nullable|string|max:255',
            'operator_id' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,normal,high,critical',
            'selectedTags' => 'nullable|array|max:10',
            'selectedTags.*' => 'exists:tags,id',
        ];
    }

    public function mount($segmentId, $cueId = null)
    {
        $this->segmentId = $segmentId;
        $this->segment = Segment::with(['session.event'])->findOrFail($segmentId);
        $this->cueId = $cueId;

        if ($cueId) {
            $this->cue = Cue::with('tags')->findOrFail($cueId);
            $this->name = $this->cue->name;
            $this->code = $this->cue->code;
            $this->cue_type_id = $this->cue->cue_type_id;
            $this->description = $this->cue->description;
            $this->time = $this->cue->time ? $this->cue->time->format('H:i') : null;
            $this->status = $this->cue->status;
            $this->notes = $this->cue->notes;
            $this->filename = $this->cue->filename;
            $this->operator_id = $this->cue->operator_id;
            $this->priority = $this->cue->priority;
            $this->selectedTags = $this->cue->tags->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'segment_id' => $this->segmentId,
            'name' => $this->name,
            'code' => $this->code,
            'cue_type_id' => $this->cue_type_id,
            'description' => $this->description,
            'time' => $this->time,
            'status' => $this->status,
            'notes' => $this->notes,
            'filename' => $this->filename,
            'operator_id' => $this->operator_id,
            'priority' => $this->priority,
        ];

        if ($this->cueId) {
            $this->cue->update($data);
            $message = 'Cue updated successfully.';
        } else {
            $this->cue = Cue::create($data);
            $message = 'Cue created successfully.';
        }

        // Sync tags
        $this->cue->tags()->sync($this->selectedTags);

        session()->flash('message', $message);
        return redirect()->route('segments.cues.index', $this->segmentId);
    }

    public function render()
    {
        // Get available cue types
        $cueTypes = CueType::active()
            ->forEvent($this->segment->session->event_id)
            ->ordered()
            ->get();

        // Get operators (event team members)
        $operators = User::whereHas('eventUsers', function ($query) {
            $query->where('event_id', $this->segment->session->event_id);
        })->orderBy('name')->get();

        // Get all tags
        $allTags = Tag::orderBy('name')->get();

        return view('livewire.cues.form', [
            'cueTypes' => $cueTypes,
            'operators' => $operators,
            'allTags' => $allTags,
        ]);
    }
}
