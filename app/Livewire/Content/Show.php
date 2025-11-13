<?php

namespace App\Livewire\Content;

use App\Models\ContentFile;
use App\Models\Event;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $contentId;
    public $event;
    public $content;

    public function mount($eventId, $contentId)
    {
        $this->eventId = $eventId;
        $this->contentId = $contentId;
        $this->event = Event::findOrFail($eventId);
        
        $this->content = ContentFile::with([
            'category',
            'creator',
            'updater',
            'tags',
            'speakers',
            'segments.session',
            'cues.segment.session',
            'versions.uploader',
            'comments.user'
        ])->findOrFail($contentId);

        // Ensure content belongs to this event
        if ($this->content->event_id != $this->eventId) {
            abort(404);
        }

        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);
    }

    public function download()
    {
        $content = ContentFile::findOrFail($this->contentId);
        
        // Generate filename based on content type
        $extension = $content->file_type === 'rich_text' ? 'html' : 'txt';
        $filename = str_replace(' ', '_', $content->name) . '.' . $extension;
        
        // Get content from metadata
        $fileContent = $content->metadata['content'] ?? '';
        
        // For rich text, wrap in basic HTML
        if ($content->file_type === 'rich_text') {
            $fileContent = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='UTF-8'>\n<title>{$content->name}</title>\n</head>\n<body>\n{$fileContent}\n</body>\n</html>";
        }
        
        return response()->streamDownload(function() use ($fileContent) {
            echo $fileContent;
        }, $filename, [
            'Content-Type' => $content->file_type === 'rich_text' ? 'text/html' : 'text/plain',
        ]);
    }

    public function render()
    {
        return view('livewire.content.show')->layout('layouts.app');
    }
}
