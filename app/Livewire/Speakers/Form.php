<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Session;
use App\Models\ContentFile;
use App\Models\CustomField;
use App\Models\Contact;
use App\Models\User;
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
    public $first_name = '';
    public $last_name = '';
    public $title = '';
    public $company = '';
    public $bio = '';
    public $notes = '';
    public $contact_person = '';
    public $email = '';
    public $headshot;
    public $existingHeadshot = null;
    public $is_active = true;
    public $contactSearch = '';
    public $contactSuggestions = [];
    public $showContactSuggestions = false;
    public $companySearch = '';
    public $companySuggestions = [];
    public $showCompanySuggestions = false;

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
    
    // Custom fields
    public $customFields = [];

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
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

    public function updatedContactPerson($value)
    {
        $value ??= '';

        if (preg_match('/@([^\s]*)$/', $value, $matches)) {
            $this->contactSearch = $matches[1];
            $shouldForce = $this->contactSearch === '';
        } else {
            $this->contactSearch = trim($value);
            $shouldForce = false;
        }

        if ($this->contactSearch === '' && !$shouldForce) {
            $this->resetContactSuggestions();
            return;
        }

        $this->loadContactSuggestions($shouldForce);
    }

    protected function loadContactSuggestions(bool $force = false): void
    {
        $search = $this->contactSearch;

        $contactsQuery = Contact::where('event_id', $this->eventId);
        $usersQuery = User::whereHas('assignedEvents', function ($q) {
            $q->where('event_id', $this->eventId);
        });

        if ($search !== '') {
            $contactsQuery->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                    ->orWhere('company', 'like', '%' . $search . '%');
            });

            $usersQuery->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        } elseif (!$force) {
            $this->resetContactSuggestions();
            return;
        }

        $contactSuggestions = $contactsQuery
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(5)
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'name' => $contact->full_name ?: trim($contact->first_name . ' ' . $contact->last_name),
                    'company' => $contact->company,
                    'email' => $contact->email,
                    'title' => $contact->title,
                    'type' => 'contact',
                ];
            });

        $userSuggestions = $usersQuery
            ->orderBy('name')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'company' => null,
                    'email' => $user->email,
                    'title' => 'Team Member',
                    'type' => 'user',
                ];
            });

        $suggestions = $contactSuggestions
            ->concat($userSuggestions)
            ->unique(function ($item) {
                return strtolower($item['type'] . '|' . $item['name'] . '|' . ($item['email'] ?? ''));
            })
            ->take(8)
            ->values()
            ->toArray();

        $this->contactSuggestions = $suggestions;
        $this->showContactSuggestions = !empty($suggestions);
    }

    public function selectContactSuggestion(string $type, int $referenceId): void
    {
        if ($type === 'user') {
            $user = User::where('id', $referenceId)
                ->whereHas('assignedEvents', function ($q) {
                    $q->where('event_id', $this->eventId);
                })
                ->first();

            if ($user) {
                $this->contact_person = $user->name;
                $this->contactSearch = '';
                $this->resetContactSuggestions();
                return;
            }
        } else {
            $contact = Contact::where('event_id', $this->eventId)
                ->where('id', $referenceId)
                ->first();

            if ($contact) {
                $this->contact_person = $contact->full_name ?: trim($contact->first_name . ' ' . $contact->last_name);

                if (!$this->company && $contact->company) {
                    $this->company = $contact->company;
                }

                $this->contactSearch = '';
                $this->resetContactSuggestions();
                return;
            }
        }

        $this->resetContactSuggestions();
    }

    public function hideContactSuggestions(): void
    {
        $this->resetContactSuggestions();
    }

    protected function resetContactSuggestions(): void
    {
        $this->contactSuggestions = [];
        $this->showContactSuggestions = false;
    }

    public function updatedCompany($value)
    {
        $this->companySearch = trim($value ?? '');

        if ($this->companySearch === '') {
            $this->resetCompanySuggestions();
            return;
        }

        $this->loadCompanySuggestions();
    }

    protected function loadCompanySuggestions(): void
    {
        $search = $this->companySearch;

        $contactCompanies = Contact::where('event_id', $this->eventId)
            ->whereNotNull('company');

        $speakerCompanies = Speaker::where('event_id', $this->eventId)
            ->whereNotNull('company');

        if ($search !== '') {
            $contactCompanies->where('company', 'like', '%' . $search . '%');
            $speakerCompanies->where('company', 'like', '%' . $search . '%');
        }

        $suggestions = $contactCompanies->pluck('company')
            ->merge($speakerCompanies->pluck('company'))
            ->filter()
            ->unique()
            ->sort()
            ->take(10)
            ->values()
            ->toArray();

        $this->companySuggestions = $suggestions;
        $this->showCompanySuggestions = !empty($suggestions);
    }

    public function selectCompanySuggestion($company): void
    {
        $this->company = $company;
        $this->companySearch = '';
        $this->resetCompanySuggestions();
    }

    public function hideCompanySuggestions(): void
    {
        $this->resetCompanySuggestions();
    }

    protected function resetCompanySuggestions(): void
    {
        $this->companySuggestions = [];
        $this->showCompanySuggestions = false;
    }

    public function mount($eventId, $speakerId = null)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        if ($speakerId) {
            $this->speakerId = $speakerId;
            $this->speaker = Speaker::with(['tags', 'sessions', 'contentFiles'])->findOrFail($speakerId);
            
            $this->first_name = $this->speaker->first_name;
            $this->last_name = $this->speaker->last_name;
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
            
            // Load custom field values
            foreach ($this->speaker->customFieldValues as $value) {
                $this->customFields[$value->custom_field_id] = $value->value;
            }
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'event_id' => $this->eventId,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
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
        
        // Save custom field values
        $speaker->syncCustomFields($this->customFields);

        // Create user account if requested
        if ($this->createUser && !$speaker->user_id && $this->email) {
            $password = $this->autoGeneratePassword ? Str::random(12) : $this->customPassword;
            
            $user = User::create([
                'name' => trim($this->first_name . ' ' . $this->last_name),
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
        
        // Get unique contact persons and companies from event contacts for autosuggest
        // Get custom fields for speakers
        $customFieldsList = CustomField::forEvent($this->eventId)
            ->forModelType('speaker')
            ->ordered()
            ->get();

        return view('livewire.speakers.form', [
            'allTags' => $allTags,
            'sessions' => $sessions,
            'contentFiles' => $contentFiles,
            'customFieldsList' => $customFieldsList,
        ]);
    }
}
