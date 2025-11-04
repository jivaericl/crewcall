<?php

namespace App\Livewire\Tags;

use App\Models\Event;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $eventId;
    public $event;
    
    // Form fields
    public $tagId = null;
    public $name = '';
    public $color = '#3B82F6';
    public $description = '';
    
    // Modal state
    public $showModal = false;
    public $showDeleteModal = false;
    public $tagToDelete = null;
    
    // Search and filter
    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'name' => 'required|string|max:255',
        'color' => 'required|string|max:7',
        'description' => 'nullable|string',
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function openCreateModal()
    {
        $this->reset(['tagId', 'name', 'color', 'description']);
        $this->color = '#3B82F6';
        $this->showModal = true;
    }

    public function openEditModal($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        $this->tagId = $tag->id;
        $this->name = $tag->name;
        $this->color = $tag->color;
        $this->description = $tag->description;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['tagId', 'name', 'color', 'description']);
    }

    public function save()
    {
        $this->validate();

        if ($this->tagId) {
            $tag = Tag::findOrFail($this->tagId);
            $tag->update([
                'name' => $this->name,
                'color' => $this->color,
                'description' => $this->description,
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message', 'Tag updated successfully.');
        } else {
            Tag::create([
                'event_id' => $this->eventId,
                'name' => $this->name,
                'color' => $this->color,
                'description' => $this->description,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            session()->flash('message', 'Tag created successfully.');
        }

        $this->closeModal();
    }

    public function confirmDelete($tagId)
    {
        $this->tagToDelete = $tagId;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->tagToDelete) {
            $tag = Tag::findOrFail($this->tagToDelete);
            $tag->delete();
            session()->flash('message', 'Tag deleted successfully.');
        }
        
        $this->showDeleteModal = false;
        $this->tagToDelete = null;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $tags = Tag::where('event_id', $this->eventId)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount(['events', 'sessions', 'segments', 'cues', 'speakers', 'contacts'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.tags.index', [
            'tags' => $tags,
        ]);
    }
}
