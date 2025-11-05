<?php

namespace App\Livewire\CueTypes;

use App\Models\CueType;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $showDeleteModal = false;
    public $cueTypeToDelete = null;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($cueTypeId)
    {
        $this->cueTypeToDelete = $cueTypeId;
        $this->showDeleteModal = true;
    }

    public function deleteCueType()
    {
        if ($this->cueTypeToDelete) {
            $cueType = CueType::find($this->cueTypeToDelete);
            
            if ($cueType && !$cueType->is_system) {
                $cueType->delete();
                session()->flash('message', 'Cue type deleted successfully.');
            } else {
                session()->flash('error', 'Cannot delete system cue types.');
            }
        }

        $this->showDeleteModal = false;
        $this->cueTypeToDelete = null;
    }

    public function render()
    {
        $query = CueType::forEvent($this->eventId);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $cueTypes = $query->ordered()->paginate(20);

        return view('livewire.cue-types.index', [
            'cueTypes' => $cueTypes,
        ]);
    }
}
