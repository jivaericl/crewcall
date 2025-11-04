<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Event;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public $eventId;
    public $contactId = null;
    public $event;
    public $contact;

    // Form fields
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $company = '';
    public $title = '';
    public $type = 'client';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip = '';
    public $country = '';
    public $notes = '';
    public $is_active = true;
    public $selectedTags = [];

    // Tag creation
    public $showTagModal = false;
    public $newTagName = '';
    public $newTagColor = '#3B82F6';

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:client,producer,vendor,staff,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'selectedTags' => 'array',
        ];
    }

    public function mount($eventId, $contactId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        $this->contactId = $contactId;

        if ($contactId) {
            $this->contact = Contact::findOrFail($contactId);
            $this->first_name = $this->contact->first_name;
            $this->last_name = $this->contact->last_name;
            $this->email = $this->contact->email;
            $this->phone = $this->contact->phone;
            $this->company = $this->contact->company;
            $this->title = $this->contact->title;
            $this->type = $this->contact->type;
            $this->address = $this->contact->address;
            $this->city = $this->contact->city;
            $this->state = $this->contact->state;
            $this->zip = $this->contact->zip;
            $this->country = $this->contact->country;
            $this->notes = $this->contact->notes;
            $this->is_active = $this->contact->is_active;
            $this->selectedTags = $this->contact->tags->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => $this->eventId,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'title' => $this->title,
            'type' => $this->type,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ];

        if ($this->contactId) {
            $this->contact->update($data);
            $message = 'Contact updated successfully.';
        } else {
            $this->contact = Contact::create($data);
            $message = 'Contact created successfully.';
        }

        // Sync tags
        $this->contact->tags()->sync($this->selectedTags);

        session()->flash('message', $message);
        return redirect()->route('events.contacts.show', [$this->eventId, $this->contact->id]);
    }

    public function openTagModal()
    {
        $this->showTagModal = true;
    }

    public function closeTagModal()
    {
        $this->showTagModal = false;
        $this->newTagName = '';
        $this->newTagColor = '#3B82F6';
    }
    
    public function createTag()
    {
        $this->validate([
            'newTagName' => 'required|string|max:255',
            'newTagColor' => 'required|string|max:7',
        ]);

        $tag = Tag::create([
            'event_id' => $this->eventId,
            'name' => $this->newTagName,
            'color' => $this->newTagColor,
        ]);

        $this->selectedTags[] = $tag->id;
        $this->closeTagModal();
        session()->flash('message', 'Tag created successfully.');
    }

    public function render()
    {
        $tags = Tag::where('event_id', $this->eventId)->orderBy('name')->get();

        return view('livewire.contacts.form', [
            'tags' => $tags,
        ]);
    }
}
