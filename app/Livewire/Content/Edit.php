<?php

namespace App\Livewire\Content;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ContentFile;
use App\Models\ContentCategory;
use App\Models\CustomField;
use App\Models\Event;
use App\Models\Speaker;
use App\Models\Segment;
use App\Models\Cue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Edit extends Component
{
    use WithFileUploads;

    public $eventId;
    public $event;
    public $contentId;
    public $content;

    // Basic fields
    public $name;
    public $description;
    public $content_text;
    public $category_id;
    public $file_type;
    public $is_active = true;

    // Relationships
    public $selectedTags = [];
    public $selectedSpeakers = [];
    public $selectedSegments = [];
    public $selectedCues = [];

    // New version upload
    public $showVersionModal = false;
    public $newVersionFile;
    public $changeNotes;
    
    // Custom fields
    public $customFields = [];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:content_categories,id',
            'file_type' => 'required|in:audio,video,presentation,document,image,rich_text,url,other',
            'content_text' => 'required_if:file_type,rich_text,url|nullable|string',
            'is_active' => 'boolean',
            'selectedTags' => 'nullable|array',
            'selectedTags.*' => 'exists:tags,id',
            'selectedSpeakers' => 'nullable|array',
            'selectedSpeakers.*' => 'exists:speakers,id',
            'selectedSegments' => 'nullable|array',
            'selectedSegments.*' => 'exists:segments,id',
            'selectedCues' => 'nullable|array',
            'selectedCues.*' => 'exists:cues,id',
        ];
    }

    public function mount($eventId, $contentId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        $this->contentId = $contentId;
        $this->content = ContentFile::with(['tags', 'speakers', 'segments', 'cues'])->findOrFail($contentId);

        // Load existing data
        $this->name = $this->content->name;
        $this->description = $this->content->description;
        $this->content_text = $this->content->content;
        $this->category_id = $this->content->category_id;
        $this->file_type = $this->content->file_type;
        $this->is_active = $this->content->is_active;

        // Load relationships
        $this->selectedTags = $this->content->tags->pluck('id')->toArray();
        $this->selectedSpeakers = $this->content->speakers->pluck('id')->toArray();
        $this->selectedSegments = $this->content->segments->pluck('id')->toArray();
        $this->selectedCues = $this->content->cues->pluck('id')->toArray();
        
        // Load custom field values
        foreach ($this->content->customFieldValues as $value) {
            $this->customFields[$value->custom_field_id] = $value->value;
        }
    }

    public function save()
    {
        $this->validate();

        $this->content->update([
            'name' => $this->name,
            'description' => $this->description,
            'content' => $this->content_text,
            'category_id' => $this->category_id,
            'file_type' => $this->file_type,
            'is_active' => $this->is_active,
        ]);

        // Sync relationships
        $this->content->tags()->sync($this->selectedTags);
        $this->content->speakers()->sync($this->selectedSpeakers);
        $this->content->segments()->sync($this->selectedSegments);
        $this->content->cues()->sync($this->selectedCues);
        
        // Save custom field values
        $this->content->syncCustomFields($this->customFields);

        session()->flash('message', 'Content updated successfully.');
        return redirect()->route('events.content.index', $this->eventId);
    }

    public function openVersionModal()
    {
        $this->showVersionModal = true;
        $this->reset(['newVersionFile', 'changeNotes']);
    }

    public function restoreVersion($versionNumber)
    {
        try {
            $this->content->restoreVersion($versionNumber);
            
            session()->flash('message', "Version {$versionNumber} has been restored as the current version.");
            
            // Refresh content
            $this->content = ContentFile::with('versions')->findOrFail($this->contentId);
        } catch (\Exception $e) {
            session()->flash('error', 'Version restoration failed: ' . $e->getMessage());
        }
    }

    public function uploadNewVersion()
    {
        $this->validate([
            'newVersionFile' => 'required|file|max:512000', // 500MB
            'changeNotes' => 'nullable|string|max:500',
        ]);

        try {
            $file = $this->newVersionFile;
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Generate unique filename
            $filename = Str::slug($this->content->name) . '-v' . ($this->content->current_version + 1) . '-' . time() . '.' . $extension;
            $path = $file->storeAs('content/' . $this->eventId, $filename, 'public');

            // Create new version using the model method
            $this->content->createNewVersion(
                $path,
                $fileSize,
                $mimeType,
                [
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $extension,
                ],
                $this->changeNotes
            );

            session()->flash('message', 'New version uploaded successfully.');
            $this->showVersionModal = false;
            $this->reset(['newVersionFile', 'changeNotes']);

            // Refresh content
            $this->content = ContentFile::findOrFail($this->contentId);
        } catch (\Exception $e) {
            session()->flash('error', 'Version upload failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = ContentCategory::active()
            ->forEvent($this->eventId)
            ->ordered()
            ->get();

        $allTags = \App\Models\Tag::where('event_id', $this->eventId)
            ->orderBy('name')
            ->get();

        $allSpeakers = Speaker::where('event_id', $this->eventId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $allSegments = Segment::whereHas('session', function($q) {
                $q->where('event_id', $this->eventId);
            })
            ->orderBy('name')
            ->get();

        $allCues = Cue::whereHas('segment.session', function($q) {
                $q->where('event_id', $this->eventId);
            })
            ->orderBy('name')
            ->get();
            
        // Get custom fields for content
        $customFieldsList = CustomField::forEvent($this->eventId)
            ->forModelType('content')
            ->ordered()
            ->get();

        return view('livewire.content.edit', [
            'categories' => $categories,
            'allTags' => $allTags,
            'allSpeakers' => $allSpeakers,
            'allSegments' => $allSegments,
            'allCues' => $allCues,
            'customFieldsList' => $customFieldsList,
        ]);
    }
}
