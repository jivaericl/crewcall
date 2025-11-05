<?php

namespace App\Livewire\ContentCategories;

use Livewire\Component;
use App\Models\ContentCategory;
use App\Models\Event;

class Index extends Component
{
    public $eventId;
    public $event;
    public $search = '';
    public $showDeleteModal = false;
    public $categoryToDelete;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function confirmDelete($categoryId)
    {
        $this->categoryToDelete = ContentCategory::findOrFail($categoryId);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->categoryToDelete) {
            // Check if category has content files
            if ($this->categoryToDelete->contentFiles()->count() > 0) {
                session()->flash('error', 'Cannot delete category with associated content files.');
                $this->showDeleteModal = false;
                return;
            }

            $this->categoryToDelete->delete();
            session()->flash('message', 'Category deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    public function render()
    {
        $categories = ContentCategory::forEvent($this->eventId)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->ordered()
            ->get();

        return view('livewire.content-categories.index', [
            'categories' => $categories,
        ]);
    }
}
