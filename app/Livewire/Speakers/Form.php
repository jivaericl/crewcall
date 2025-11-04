<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Models\Event;
use App\Models\User;
use App\Models\Tag;
use App\Models\Session;
use App\Models\ContentFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Form extends Component
{
    use WithFileUploads;

    public $eventId;
    public $event;
    public $speakerId = null;
    public $speaker;

    // Speaker fields
    public $name = '';
    public $title = '';
    public $company = '';
    public $bio = '';
    public $notes = '';
    public $contact_person = '';
    public $email = '';
    public $headshot;
    public $existingHeadshot = null;
    public $is_active = true;

    // Relationships
    public $selectedTags = [];
    public $selectedSessions = [];
    public $selectedContent = [];

    // User creation
    public $createUser = false;
    public $userRole = 'viewer';
    public $autoGeneratePassword = true;
    public $customPassword = '';
    public $sendWelcomeEmail = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'notes' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'headshot' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'selectedTags' => 'array',
            'selectedSessions' => 'array',
            'selectedContent' => 'array',
        ];
    }

    public function mount($eventId, $speakerId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        if ($speakerId) {
            $this->speakerId = $speakerId;
            $this->speaker = Speaker::with(['tags', 'sessions', 'contentFiles'])->findOrFail($speakerId);
            
            $this->name = $this->speaker->name;
            $this->title = $this->speaker->title;
            $this->company = $this->speaker->company;
            $this->bio = $this->speaker->bio;
            $this->notes = $this->speaker->notes;
            $this->contact_person = $this->speaker->contact_person;
            $this->email = $this->speaker->email;
            $this->existingHeadshot = $this->speaker->headshot_path;
            $this->is_active = $this->speaker->is_active;
            
            $this->selectedTags = $this->speaker->tags->pluck('id')->toArray();
            $this->selectedSessions = $this->speaker->sessions->pluck('id')->toArray();
            $this->selectedContent = $this->speaker->contentFiles->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => $this->eventId,
            'name' => $this->name,
            'title' => $this->title,
            'company' => $this->company,
            'bio' => $this->bio,
            'notes' => $this->notes,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        // Handle headshot upload
        if ($this->headshot) {
            $path = $this->headshot->store('headshots', 'public');
            $data['headshot_path'] = $path;
        }

        if ($this->speakerId) {
            $speaker = Speaker::findOrFail($this->speakerId);
            $speaker->update($data);
        } else {
            $speaker = Speaker::create($data);
        }

        // Sync relationships
        $speaker->tags()->sync($this->selectedTags);
        $speaker->sessions()->sync($this->selectedSessions);
        $speaker->contentFiles()->sync($this->selectedContent);

        // Create user account if requested
        if ($this->createUser && !$speaker->user_id && $this->email) {
            $password = $this->autoGeneratePassword ? Str::random(12) : $this->customPassword;
            
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($password),
            ]);

            $speaker->update(['user_id' => $user->id]);

            // Add to event team with appropriate role
            $roleId = $this->userRole === 'viewer' ? $this->getViewerRoleId() : $this->getUserRoleId();
            $this->event->users()->attach($user->id, ['role_id' => $roleId]);

            // TODO: Send welcome email if requested
            if ($this->sendWelcomeEmail) {
                // Mail::to($user)->send(new WelcomeEmail($user, $password));
            }

            session()->flash('message', 'Speaker saved and user account created successfully.');
        } else {
            session()->flash('message', 'Speaker saved successfully.');
        }

        return redirect()->route('events.speakers.show', ['eventId' => $this->eventId, 'speakerId' => $speaker->id]);
    }

    private function getViewerRoleId()
    {
        return \App\Models\Role::where('name', 'Client')->first()->id ?? null;
    }

    private function getUserRoleId()
    {
        return \App\Models\Role::where('name', 'Content Producer')->first()->id ?? null;
    }

    public function render()
    {
        $allTags = Tag::orderBy('name')->get();
        $sessions = Session::where('event_id', $this->eventId)->orderBy('start_date')->get();
        $contentFiles = ContentFile::where('event_id', $this->eventId)->orderBy('name')->get();

        return view('livewire.speakers.form', [
            'allTags' => $allTags,
            'sessions' => $sessions,
            'contentFiles' => $contentFiles,
        ]);
    }
}
