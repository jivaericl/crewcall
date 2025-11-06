<?php

namespace App\Livewire\Cues;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cue;
use App\Models\Segment;

class Index extends Component
{
    use WithPagination;

    public $segmentId;
    public $segment;
    public $search = '';
    public $statusFilter = '';
    public $priorityFilter = '';
    public $cueTypeFilter = '';
    public $deleteId;

    protected $queryString = ['search', 'statusFilter', 'priorityFilter', 'cueTypeFilter'];

    public function mount($segmentId)
    {
        $this->segmentId = $segmentId;
        $this->segment = Segment::with(['session.event'])->findOrFail($segmentId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingCueTypeFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deleteCue()
    {
        $cue = Cue::findOrFail($this->deleteId);
        $cue->delete();
        
        $this->deleteId = null;
        session()->flash('message', 'Cue deleted successfully.');
    }

    public function duplicateCue($id)
    {
        $originalCue = Cue::with('tags')->findOrFail($id);
        
        $newCue = $originalCue->replicate();
        $newCue->name = $originalCue->name . ' (Copy)';
        $newCue->status = 'standby'; // Reset status for duplicate
        $newCue->save();
        
        // Duplicate tags
        $newCue->tags()->attach($originalCue->tags->pluck('id'));
        
        session()->flash('message', 'Cue duplicated successfully.');
    }

    public function activateCue($cueId)
    {
        // Find the cue being activated
        $cue = Cue::findOrFail($cueId);
        
        // If this cue is already GO, do nothing
        if ($cue->status === 'go') {
            return;
        }
        
        // Find any cue that is currently GO in this segment and mark it as complete
        Cue::where('segment_id', $this->segmentId)
            ->where('status', 'go')
            ->update(['status' => 'complete']);
        
        // Set this cue to GO
        $cue->status = 'go';
        $cue->save();
        
        session()->flash('message', 'Cue activated.');
    }
    
    public function updateStatus($cueId, $status)
    {
        $cue = Cue::findOrFail($cueId);
        $cue->status = $status;
        $cue->save();
        
        session()->flash('message', 'Cue status updated.');
    }

    public function render()
    {
        $query = Cue::with(['cueType', 'operator', 'tags', 'updater'])
            ->where('segment_id', $this->segmentId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->cueTypeFilter) {
            $query->where('cue_type_id', $this->cueTypeFilter);
        }

        $cues = $query->ordered()->paginate(20);

        // Get available cue types for filter
        $cueTypes = \App\Models\CueType::active()
            ->forEvent($this->segment->session->event_id)
            ->ordered()
            ->get();

        return view('livewire.cues.index', [
            'cues' => $cues,
            'cueTypes' => $cueTypes,
        ]);
    }
}
