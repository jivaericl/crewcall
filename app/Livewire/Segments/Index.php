<?php

namespace App\Livewire\Segments;

use App\Models\Segment;
use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $sessionId;
    public $session;
    public $search = '';
    public $showDeleteModal = false;
    public $segmentToDelete = null;

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->session = Session::with('event')->findOrFail($sessionId);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($segmentId)
    {
        $this->segmentToDelete = $segmentId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->segmentToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteSegment()
    {
        if ($this->segmentToDelete) {
            $segment = Segment::findOrFail($this->segmentToDelete);
            $name = $segment->name;
            $segment->delete();
            
            session()->flash('message', "Segment \"{$name}\" deleted successfully.");
            
            $this->segmentToDelete = null;
            $this->showDeleteModal = false;
        }
    }

    public function duplicateSegment($segmentId)
    {
        $segment = Segment::with('tags')->findOrFail($segmentId);
        
        if ($segment->session_id == $this->sessionId) {
            $newSegment = $segment->replicate();
            $newSegment->name = $segment->name . ' (Copy)';
            $newSegment->save();
            
            // Copy tags
            $newSegment->tags()->sync($segment->tags->pluck('id')->toArray());
            
            session()->flash('message', "Segment \"{$segment->name}\" duplicated successfully.");
        }
    }

    public function render()
    {
        $query = Segment::with(['client', 'producer', 'creator', 'updater', 'tags'])
            ->where('session_id', $this->sessionId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $segments = $query->ordered()->paginate(15);

        return view('livewire.segments.index', [
            'segments' => $segments,
        ]);
    }
}
