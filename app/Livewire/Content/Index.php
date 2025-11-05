<?php

namespace App\Livewire\Content;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ContentFile;
use App\Models\ContentCategory;
use App\Models\Event;
use App\Models\Speaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $eventId;
    public $event;
    public $search = '';
    public $typeFilter = '';
    public $categoryFilter = '';
    public $deleteId;
    public $viewingVersions;
    
    // Upload properties
    public $showUploadModal = false;
    public $uploadFile;
    public $uploadName;
    public $uploadDescription;
    public $uploadCategory;
    public $uploadType;
    public $uploadSpeakers = [];
    public $uploadSegments = [];
    public $uploadCues = [];

    protected $queryString = ['search', 'typeFilter', 'categoryFilter'];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function openUploadModal()
    {
        $this->showUploadModal = true;
        $this->reset(['uploadFile', 'uploadName', 'uploadDescription', 'uploadCategory', 'uploadType', 'uploadSpeakers', 'uploadSegments', 'uploadCues']);
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->reset(['uploadFile', 'uploadName', 'uploadDescription', 'uploadCategory', 'uploadType', 'uploadSpeakers', 'uploadSegments', 'uploadCues']);
    }

    public function uploadFileSubmit()
    {
        $this->validate([
            'uploadFile' => 'required|file|max:512000', // 500MB max
            'uploadName' => 'required|string|min:3|max:255',
            'uploadDescription' => 'nullable|string',
            'uploadCategory' => 'nullable|exists:content_categories,id',
            'uploadType' => 'required|in:audio,video,presentation,document,image,other',
        ]);

        try {
            $file = $this->uploadFile;
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Generate unique filename
            $filename = Str::slug($this->uploadName) . '-' . time() . '.' . $extension;
            $path = $file->storeAs('content/' . $this->eventId, $filename, 'public');

            // Auto-categorize headshots
            $categoryId = $this->uploadCategory;
            if (empty($categoryId) && $this->uploadType === 'image' && 
                (stripos($this->uploadName, 'headshot') !== false || 
                 stripos($originalName, 'headshot') !== false)) {
                // Find or create Headshots category
                $headshotCategory = ContentCategory::firstOrCreate(
                    [
                        'event_id' => $this->eventId,
                        'name' => 'Headshots',
                    ],
                    [
                        'description' => 'Speaker and staff headshots',
                        'color' => '#8B5CF6', // Purple
                        'is_active' => true,
                    ]
                );
                $categoryId = $headshotCategory->id;
            }

            // Create content file record
            $contentFile = ContentFile::create([
                'event_id' => $this->eventId,
                'category_id' => $categoryId,
                'name' => $this->uploadName,
                'description' => $this->uploadDescription,
                'file_type' => $this->uploadType,
                'mime_type' => $mimeType,
                'current_file_path' => $path,
                'current_file_size' => $fileSize,
                'current_version' => 1,
                'metadata' => [
                    'original_name' => $originalName,
                    'extension' => $extension,
                ],
            ]);

            // Create initial version record
            \App\Models\ContentFileVersion::create([
                'content_file_id' => $contentFile->id,
                'version_number' => 1,
                'file_path' => $path,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'metadata' => [
                    'original_name' => $originalName,
                    'extension' => $extension,
                ],
                'change_notes' => 'Initial upload',
                'uploaded_by' => auth()->id(),
            ]);
            
            // Sync relationships
            if (!empty($this->uploadSpeakers)) {
                $contentFile->speakers()->sync($this->uploadSpeakers);
            }
            if (!empty($this->uploadSegments)) {
                $contentFile->segments()->sync($this->uploadSegments);
            }
            if (!empty($this->uploadCues)) {
                $contentFile->cues()->sync($this->uploadCues);
            }

            session()->flash('message', 'File uploaded successfully.');
            $this->closeUploadModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deleteFile()
    {
        $file = ContentFile::findOrFail($this->deleteId);
        
        // Delete physical file
        if (Storage::disk('public')->exists($file->current_file_path)) {
            Storage::disk('public')->delete($file->current_file_path);
        }
        
        // Delete all version files
        foreach ($file->versions as $version) {
            if (Storage::disk('public')->exists($version->file_path)) {
                Storage::disk('public')->delete($version->file_path);
            }
        }
        
        $file->delete();
        
        $this->deleteId = null;
        session()->flash('message', 'File deleted successfully.');
    }

    public function viewVersions($id)
    {
        $this->viewingVersions = $id;
    }

    public function closeVersions()
    {
        $this->viewingVersions = null;
    }

    public function render()
    {
        $query = ContentFile::with(['category', 'creator', 'updater'])
            ->where('event_id', $this->eventId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('file_type', $this->typeFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $files = $query->ordered()->paginate(15);

        $categories = ContentCategory::active()
            ->forEvent($this->eventId)
            ->ordered()
            ->get();

        $versionedFile = null;
        if ($this->viewingVersions) {
            $versionedFile = ContentFile::with('versions.uploader')->findOrFail($this->viewingVersions);
        }
        
        // Get all speakers for this event
        $allSpeakers = Speaker::where('event_id', $this->eventId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get all segments for this event
        $allSegments = \App\Models\Segment::whereHas('session', function($q) {
                $q->where('event_id', $this->eventId);
            })
            ->orderBy('name')
            ->get();

        // Get all cues for this event
        $allCues = \App\Models\Cue::whereHas('segment.session', function($q) {
                $q->where('event_id', $this->eventId);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.content.index', [
            'files' => $files,
            'categories' => $categories,
            'versionedFile' => $versionedFile,
            'allSpeakers' => $allSpeakers,
            'allSegments' => $allSegments,
            'allCues' => $allCues,
        ]);
    }
}
