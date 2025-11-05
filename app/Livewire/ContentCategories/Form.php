<?php

namespace App\Livewire\ContentCategories;

use Livewire\Component;
use App\Models\ContentCategory;
use App\Models\Event;

class Form extends Component
{
    public $eventId;
    public $event;
    public $categoryId;
    public $category;

    public $name;
    public $description;
    public $color = '#3b82f6';
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ];
    }

    public function mount($eventId, $categoryId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        $this->categoryId = $categoryId;

        if ($categoryId) {
            $this->category = ContentCategory::findOrFail($categoryId);
            $this->name = $this->category->name;
            $this->description = $this->category->description;
            $this->color = $this->category->color ?? '#3b82f6';
            $this->is_active = $this->category->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => $this->eventId,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'is_active' => $this->is_active,
            'is_system' => false,
        ];

        if ($this->categoryId) {
            $this->category->update($data);
            $message = 'Category updated successfully.';
        } else {
            ContentCategory::create($data);
            $message = 'Category created successfully.';
        }

        session()->flash('message', $message);
        return redirect()->route('events.content-categories.index', $this->eventId);
    }

    public function render()
    {
        return view('livewire.content-categories.form');
    }
}
