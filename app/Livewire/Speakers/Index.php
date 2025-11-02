<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    public $search = '';
    public $filterTag = '';
    public $filterActive = 'all';
    public $showDeleteModal = false;
    public $speakerToDelete = null;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTag()
    {
        $this->resetPage();
    }

    public function updatingFilterActive()
    {
        $this->resetPage();
    }

    public function confirmDelete($speakerId)
    {
        $this->speakerToDelete = $speakerId;
        $this->showDeleteModal = true;
    }

    public function deleteSpeaker()
    {
        if ($this->speakerToDelete) {
            $speaker = Speaker::findOrFail($this->speakerToDelete);
            $speaker->delete();
            
            session()->flash('message', 'Speaker deleted successfully.');
            $this->showDeleteModal = false;
            $this->speakerToDelete = null;
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->speakerToDelete = null;
    }

    public function render()
    {
        $query = Speaker::forEvent($this->eventId)
            ->with(['sessions', 'contentFiles', 'tags', 'user']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterTag) {
            $query->whereHas('tags', function($q) {
                $q->where('tags.id', $this->filterTag);
            });
        }

        if ($this->filterActive === 'active') {
            $query->active();
        } elseif ($this->filterActive === 'inactive') {
            $query->where('is_active', false);
        }

        $speakers = $query->orderBy('name')->paginate(15);

        $allTags = \App\Models\Tag::orderBy('name')->get();

        return view('livewire.speakers.index', [
            'speakers' => $speakers,
            'allTags' => $allTags,
        ]);
    }
}
