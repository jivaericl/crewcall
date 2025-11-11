<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Event;
use App\Models\Tag;
use App\Models\CustomField;
use App\Models\Session;
use App\Models\ContentFile;
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
    public $contact_type = 'client';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip = '';
    public $country = '';
    public $notes = '';
    public $is_active = true;
    public $selectedTags = [];
    public $selectedSessions = [];
    public $selectedContentFiles = [];

    // Tag creation
    public $showTagModal = false;
    public $newTagName = '';
    public $newTagColor = '#3B82F6';

    // Custom fields
    public $customFields = [];

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'contact_type' => 'required|in:client,producer,vendor,staff,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'selectedTags' => 'array',
            'selectedSessions' => 'array',
            'selectedContentFiles' => 'array',
            'customFields.*' => 'nullable',
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
            $this->contact_type = $this->contact->contact_type;
            $this->address = $this->contact->address;
            $this->city = $this->contact->city;
            $this->state = $this->contact->state;
            $this->zip = $this->contact->zip;
            $this->country = $this->contact->country;
            $this->notes = $this->contact->notes;
            $this->is_active = $this->contact->is_active;
            $this->selectedTags = $this->contact->tags->pluck('id')->toArray();
            $this->selectedSessions = $this->contact->sessions->pluck('id')->toArray();
            $this->selectedContentFiles = $this->contact->contentFiles->pluck('id')->toArray();

            // Load custom field values
            foreach ($this->contact->customFieldValues as $value) {
                $this->customFields[$value->custom_field_id] = $value->value;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'event_id' => $this->eventId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'title' => $this->title,
                'contact_type' => $this->contact_type,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country' => $this->country,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ];

            // Use database transaction to ensure all operations succeed or fail together
            \DB::beginTransaction();

            if ($this->contactId) {
                $this->contact->update($data);
                $message = 'Contact updated successfully.';
            } else {
                $this->contact = Contact::create($data);
                $message = 'Contact created successfully.';
            }

            // Sync tags
            $this->contact->tags()->sync($this->selectedTags);

            // Sync sessions
            $this->contact->sessions()->sync($this->selectedSessions);

            // Sync content files
            $this->contact->contentFiles()->sync($this->selectedContentFiles);

            // Save custom field values
            $this->contact->syncCustomFields($this->customFields);

            \DB::commit();

            session()->flash('message', $message);
            return redirect()->route('events.contacts.show', [$this->eventId, $this->contact->id]);
        } catch (\Exception $e) {
            \DB::rollBack();

            // Log the error
            \Log::error('Error saving contact: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'event_id' => $this->eventId,
                'contact_id' => $this->contactId,
                'exception' => $e,
            ]);

            session()->flash('error', 'An error occurred while saving the contact. Please try again or contact support.');
            return null;
        }
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

        try {
            $tag = Tag::create([
                'event_id' => $this->eventId,
                'name' => $this->newTagName,
                'color' => $this->newTagColor,
            ]);

            $this->selectedTags[] = $tag->id;
            $this->closeTagModal();
            session()->flash('message', 'Tag created successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating tag: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'event_id' => $this->eventId,
                'tag_name' => $this->newTagName,
                'exception' => $e,
            ]);

            session()->flash('error', 'An error occurred while creating the tag. Please try again.');
        }
    }

    public function render()
    {
        $tags = Tag::where('event_id', $this->eventId)->orderBy('name')->get();

        // Get sessions for this event
        $sessions = Session::where('event_id', $this->eventId)
            ->orderBy('start_date')
            ->get();

        // Get content files for this event
        $contentFiles = ContentFile::where('event_id', $this->eventId)
            ->orderBy('name')
            ->get();

        // Get custom fields for contacts
        $customFieldsList = CustomField::forEvent($this->eventId)
            ->forModelType('contact')
            ->ordered()
            ->get();

        return view('livewire.contacts.form', [
            'tags' => $tags,
            'sessions' => $sessions,
            'contentFiles' => $contentFiles,
            'customFieldsList' => $customFieldsList,
        ]);
    }
}
