<?php

namespace App\Livewire\CustomFields;

use App\Models\CustomField;
use App\Models\Event;
use Livewire\Component;

class Form extends Component
{
    public $eventId;
    public $event;
    public $fieldId;
    public $name = '';
    public $field_type = 'text';
    public $is_required = false;
    public $options = '';
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|string|max:255',
        'field_type' => 'required|in:text,number,date,select,checkbox',
        'is_required' => 'boolean',
        'options' => 'nullable|string',
        'sort_order' => 'integer|min:0',
    ];

    protected $messages = [
        'name.required' => 'Field name is required.',
        'field_type.required' => 'Field type is required.',
        'options.required_if' => 'Options are required for select fields.',
    ];

    public function mount($eventId, $fieldId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        if ($fieldId) {
            $this->fieldId = $fieldId;
            $field = CustomField::where('event_id', $eventId)->findOrFail($fieldId);
            
            $this->name = $field->name;
            $this->field_type = $field->field_type;
            $this->is_required = $field->is_required;
            $this->options = is_array($field->options) ? implode("\n", $field->options) : '';
            $this->sort_order = $field->sort_order;
        } else {
            // Set default sort order to be last
            $this->sort_order = CustomField::where('event_id', $eventId)->max('sort_order') + 1;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        // Process options for select field type
        $optionsArray = null;
        if ($this->field_type === 'select' && $this->options) {
            $optionsArray = array_filter(array_map('trim', explode("\n", $this->options)));
            
            if (empty($optionsArray)) {
                $this->addError('options', 'Please provide at least one option for the select field.');
                return;
            }
        }

        if ($this->fieldId) {
            // Update existing field
            $field = CustomField::where('event_id', $this->eventId)->findOrFail($this->fieldId);
            $field->update([
                'name' => $this->name,
                'field_type' => $this->field_type,
                'is_required' => $this->is_required,
                'options' => $optionsArray,
                'sort_order' => $this->sort_order,
            ]);
            
            $message = "Custom field \"{$field->name}\" updated successfully.";
        } else {
            // Create new field
            $field = CustomField::create([
                'event_id' => $this->eventId,
                'name' => $this->name,
                'field_type' => $this->field_type,
                'is_required' => $this->is_required,
                'options' => $optionsArray,
                'sort_order' => $this->sort_order,
            ]);
            
            $message = "Custom field \"{$field->name}\" created successfully.";
        }

        session()->flash('message', $message);
        
        return redirect()->route('custom-fields.index', $this->eventId);
    }

    public function render()
    {
        $fieldTypes = CustomField::getFieldTypes();

        return view('livewire.custom-fields.form', [
            'fieldTypes' => $fieldTypes,
        ]);
    }
}
