<?php

namespace App\Livewire\CustomFields;

use App\Models\CustomField;
use App\Models\Event;
use Livewire\Component;

class Index extends Component
{
    public $eventId;
    public $event;
    public $filterModelType = '';
    public $showDeleteModal = false;
    public $fieldToDelete = null;

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function confirmDelete($fieldId)
    {
        $this->fieldToDelete = $fieldId;
        $this->showDeleteModal = true;
    }

    public function deleteField()
    {
        if ($this->fieldToDelete) {
            $field = CustomField::find($this->fieldToDelete);
            if ($field && $field->event_id == $this->eventId) {
                $fieldName = $field->name;
                $field->delete();
                session()->flash('message', "Custom field \"{$fieldName}\" deleted successfully.");
            }
        }
        
        $this->showDeleteModal = false;
        $this->fieldToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->fieldToDelete = null;
    }

    public function render()
    {
        $query = CustomField::forEvent($this->eventId);
        
        if ($this->filterModelType) {
            $query->forModelType($this->filterModelType);
        }
        
        $customFields = $query->ordered()->get();

        return view('livewire.custom-fields.index', [
            'customFields' => $customFields,
            'modelTypes' => CustomField::getModelTypes(),
        ]);
    }
}
